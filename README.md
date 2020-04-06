# Smsconnect

<img align="right" src="http://www.smsbrana.cz/images/logo.png">

Send and receive SMS with PHP (for Czech Republic)

[Registration](http://www.smsbrana.cz/registrace.html)

## Installation

via composer:

    $ composer require smsbrana/sms-connect

## Usage

### Inbox

```php
use \Neogate\SmsConnect\SmsConnect;
$smsConnect = new SmsConnect('<your_login>', '<secret_password>');
$smsConnect->getInbox();
```

### Send SMS

```php
use \Neogate\SmsConnect\SmsConnect;
$smsConnect = new SmsConnect('<your_login>', '<secret_password>');
$smsConnect->sendSms('<phone_number>', '<text_sms>');
```

### Send bulk
```php
$smsConnect->addRecipient('<phone_number>', '<text_sms>');
$smsConnect->addRecipient('<another_number>', '<another_sms>');
$smsConnect->sendBulk();
```
## Using as extension in Nette Framework

config.neon
```yml
extensions:
	smsconnect: Neogate\SmsConnect\SmsConnectExtension
```

config.local.neon
```yml
smsconnect:
	login: 'your_login'
	password: 'secret_password'
```

finally inject extension
```php
/** @var SmsConnect */
private $smsConnect;

public function injectSmsConnectExtension(SmsConnect $smsConnect)
{
    $this->smsConnect = $smsConnect;
}
```
