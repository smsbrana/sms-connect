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
$smsConnect = new SmsConnect('<your_login>', '<secret_password>');
$smsConnect->getInbox();
```

### Send SMS

```php
$smsConnect = new SmsConnect('<your_login>', '<secret_password>');
$smsConnect->sendSms('<phone_number>', '<text_sms>');
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
