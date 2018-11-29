<?php

namespace Neogate\SmsConnect;


use SimpleXMLElement;


class SmsConnect
{

	/** @var string */
	private $login;

	/** @var string */
	private $password;

	/** @var \SimpleXMLElement */
	private $queue;

	const API_URL = 'https://api.smsbrana.cz/smsconnect/http.php';

	const ACTION_SEND_SMS = 'send_sms';

	const ACTION_SEND_BULK = 'xml_queue';

	const ACTION_INBOX = 'inbox';

	const USER_AGENT = 'SmsConnect PHP v2.0';

	private $priorities = array(-1, 0, 1, 2, 3);


	/**
	 * @param string $login
	 * @param string $password
	 */
	public function __construct($login, $password)
	{
		if ($login === NULL) {
			throw new InvalidArgumentException('Empty login');
		}

		if ($password === NULL) {
			throw new InvalidArgumentException('Empty password');
		}

		$this->login = $login;
		$this->password = $password;
	}


	/**
	 * @return array
	 */
	public function getInbox()
	{
		$authData = $this->getAuth($this->login, $this->password);
		$authData['action'] = self::ACTION_INBOX;

		$requestUrl = $this->getRequestUrl($authData);
		$response = $this->getRequest($requestUrl);

		return $response;
	}


	/**
	 * @param string $number phone number of receiver
	 * @param string $text message for receiver
	 * @param string $sender
	 * @param string|NULL $userId
	 * @param int $deliveryReport
	 * @param int $priority
	 * @return array
	 */
	public function sendSms($number, $text, $sender = '', $userId = NULL, $deliveryReport = 1, $priority = 1)
	{
		$authData = $this->getAuth($this->login, $this->password);
		$authData['action'] = self::ACTION_SEND_SMS;
		$authData['number'] = $number;
		$authData['message'] = urlencode($text);
		$authData['sender_id'] = $sender;
		$authData['user_id'] = $userId;
		$authData['delivery_report'] = $deliveryReport;
		if (in_array($priority, $this->priorities)) {
			throw new InvalidArgumentException('Incorrect priority argument');
		}
		$authData['priority'] = $priority;

		$requestUrl = $this->getRequestUrl($authData);
		$response = $this->getRequest($requestUrl);

		return $response;
	}

	/**
	 * @param string $number
	 * @param string $text
	 * @param string|NULL $time
	 * @param string $sender
	 * @param string|NULL $userId
	 * @param int $deliveryReport
	 * @param int $priority
	 */
	public function addRecipient($number, $text, $time = NULL, $sender = '', $userId = NULL, $deliveryReport = 1, $priority = 1)
	{
		if (!$this->queue) {
			$this->queue = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><queue></queue>');
		}
		$sms = $this->queue->addChild("sms");
		$sms->addChild("number", $this->xmlEncode($number));
		$sms->addChild("message", $this->xmlEncode($text));
		$sms->addChild("when", $this->xmlEncode($time));
		$sms->addChild("sender_id", $this->xmlEncode($sender));
		$sms->addChild("delivery_report", $deliveryReport);
		$sms->addChild('user_id', $userId);
		if (in_array($priority, $this->priorities)) {
			throw new InvalidArgumentException('Incorrect priority argument');
		}
		$sms->addChild('priority', $priority);
	}


	public function sendBulk()
	{
		$authData = $this->getAuth($this->login, $this->password);
		$authData['action'] = self::ACTION_SEND_BULK;

		$requestUrl = $this->getRequestUrl($authData);

		return $this->getRequest($requestUrl, 'POST', $this->queue->asXML());
	}


	/**
	 * @param string $login
	 * @param string $password
	 * @return array
	 */
	protected function getAuth($login, $password)
	{
		$time = date("Ymd")."T".date("His");
		$salt = $this->getSalt(10);

		$authData = array(
			'login' => $login,
			'sul' => $salt,
			'time' => $time,
			'hash' => md5($password . $time . $salt),
		);

		return $authData;
	}


	/**
	 * @param array $authData
	 * @return string
	 */
	protected function getRequestUrl($authData)
	{
		$get = array();
		foreach ($authData as $key => $item) {
			$get[] = $key . '=' . $item;
		}

		return self:: API_URL . '?' . implode('&', $get);
	}


	/**
	 * @param int $length
	 * @return string
	 */
	protected function getSalt($length = 10) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
	    $string = '';
	    for ($p = 0; $p < $length; $p++) {
	        $string .= $characters[mt_rand(0, strlen($characters) - 1)];
	    }

	    return $string;
	}


	/**
	 * @param string $url
	 * @param string $method
	 * @param string $data|NULL
	 * @return mixed
	 */
	protected function makeRequest($url, $method = 'GET', $data = NULL)
	{
		$curl = curl_init();

		$curlOpt = array(
		    CURLOPT_RETURNTRANSFER => 1,
		    CURLOPT_URL => $url,
		    CURLOPT_USERAGENT => self::USER_AGENT,
		);

		if ($method === 'POST') {
			$curlOpt[CURLOPT_POST] = 1;
			$curlOpt[CURLOPT_POSTFIELDS] = $data;
		}

		curl_setopt_array($curl, $curlOpt);
		$response = curl_exec($curl);
		curl_close($curl);

		$response = $this->convertToArray(simplexml_load_string($response));

		return $response;
	}


	/**
	 * @param string $url
	 * @param string $method
	 * @param string $data|NULL
	 * @return array
	 */
	protected function getRequest($url, $method = 'GET', $data = NULL)
	{
		$response = $this->makeRequest($url, $method, $data);
		$this->validateResponse($response);

		return $response;
	}


	/**
	 * @param array $response
	 */
	protected function validateResponse($response)
	{
		if (isset($response['err'])) {
			if ($response['err'] === '1') {
				throw new RuntimeException('Unknown error');

			} elseif ($response['err'] === '2' || $response['err'] === '3') {
				throw new MemberAccessException('Incorrect login or password');

			} elseif ($response['err'] === '5') {
				throw new InvalidStateException('Disallowed remote IP, see your SmsConnect setting');

			} elseif ($response['err'] === '8') {
				throw new InvalidStateException('Database connection error');

			} elseif ($response['err'] === '9') {
				throw new InvalidStateException('No credit');

			} elseif ($response['err'] === '10') {
				throw new InvalidArgumentException('Invalid recipient number');

			} elseif ($response['err'] === '11') {
				throw new InvalidArgumentException('Empty sms text');

			} elseif ($response['err'] === '12') {
				throw new InvalidArgumentException('Text is too long, allowed maximum is 495 chars');

			}
		}
	}


	/**
	 * @param SimpleXMLElement $xml
	 * @return array
	 */
	protected function convertToArray($xml)
	{
		foreach ( (array) $xml as $index => $node ) {
			$out[$index] = (is_object($node)) ? $this->convertToArray($node) : $node;
		}

		return $out;
	}

	/**
	 * @param $string
	 * @return string
	 */
	protected function xmlEncode($string)
	{
		return htmlspecialchars(preg_replace('#[\x00-\x08\x0B\x0C\x0E-\x1F]+#', '', $string), ENT_QUOTES);
	}

}
