# Smsconnect

<img align="right" src="http://www.smsbrana.cz/images/logo.png">

Send and receive SMS with PHP (for Czech Republic)

[Registration](http://www.smsbrana.cz/registrace.html)

## Installation

via composer:

    $ composer require pryznar/sms-connect

## Usage

### Inbox

```php
$smsConnect = new SmsConnect('<your_login>', '<secret_password>');
$smsConnect->getInbox();
```

### Send SMS

```ruby
require 'smsconnect'

$smsConnect = new SmsConnect('<your_login>', '<secret_password>');
$smsConnect->sendSms('<phone_number>', '<text_sms>');
```

## Contributing

1. Fork it ( https://github.com/[my-github-username]/smsconnect/fork )
2. Create your feature branch (`git checkout -b my-new-feature`)
3. Commit your changes (`git commit -am 'Add some feature'`)
4. Push to the branch (`git push origin my-new-feature`)
5. Create a new Pull Request
