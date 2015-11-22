<?php

namespace Neogate\SmsConnect;


class SmsConnect
{

	/** @var array */
	private $authData;

	const API_URL = 'http://api.smsbrana.cz/smsconnect/http.php';

	const ACTION_SEND_SMS = 'send_sms';

	const ACTION_INBOX = 'inbox';

	const USER_AGENT = 'SmsConnect PHP v2.0';


	/**
	 * @param string $login
	 * @param string $password
	 */
	public function __construct($login, $password)
	{
		$this->authData = $this->getAuth($login, $password);
	}


	/**
	 * @return array
	 */
	public function getInbox()
	{
		$this->authData['action'] = self::ACTION_INBOX;

		$requestUrl = $this->getRequestUrl($this->authData);
		$response = $this->makeRequest($requestUrl);

		return $this->convertToArray($response);
	}


	/**
	 * @param string $number phone number of receiver
	 * @param string $text message for receiver
	 * @return array
	 */
	public function sendSms($number, $text)
	{
		$this->authData['action'] = self::ACTION_SEND_SMS;
		$this->authData['number'] = $number;
		$this->authData['message'] = urlencode($text);

		$requestUrl = $this->getRequestUrl($this->authData);
		$response = $this->makeRequest($requestUrl);

		return $this->convertToArray($response);
	}


	/**
	 * @param string $login
	 * @param string $password
	 * @return array
	 */
	protected function getAuth($login, $password)
	{
		if ($login === NULL) {
			throw new InvalidArgumentException('Empty login');
		}

		if ($password === NULL) {
			throw new InvalidArgumentException('Empty password');
		}

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
	        $string .= $characters[mt_rand(0, strlen($characters))];
	    }

	    return $string;
	}


	/**
	 * @param $url
	 * @return mixed
	 */
	protected function makeRequest($url)
	{
		$curl = curl_init();
		curl_setopt_array($curl, array(
		    CURLOPT_RETURNTRANSFER => 1,
		    CURLOPT_URL => $url,
		    CURLOPT_USERAGENT => self::USER_AGENT,
		));
		$resp = curl_exec($curl);
		curl_close($curl);

		return $resp;
	}


	/**
	 * @param string $xmlString
	 * @return array
	 */
	protected function convertToArray($xmlString)
	{
		$xml = simplexml_load_string($xmlString);
		$json = json_encode($xml);
		return json_decode($json,TRUE);
	}

}
