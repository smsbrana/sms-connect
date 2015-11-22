<?php

use Neogate\SmsConnect\SmsConnect;
use Tester\Assert;

$container = require __DIR__ . '/../bootstrap.php';


// empty auth
Assert::exception(function() {
    new SmsConnect(NULL, NULL);
}, 'Neogate\SmsConnect\InvalidArgumentException', 'Empty login');


// Incorrect login or password
$smsConnect = Mockery::mock(SmsConnect::class)
	->makePartial()
	->shouldAllowMockingProtectedMethods();
$smsConnect->shouldReceive('makeRequest')->andReturn(['err' => '3']);

Assert::exception(function() use ($smsConnect) {
	$smsConnect->getInbox();
}, 'Neogate\SmsConnect\MemberAccessException', 'Incorrect login or password');


// Empty sms text
$smsConnect = Mockery::mock(SmsConnect::class)
	->makePartial()
	->shouldAllowMockingProtectedMethods();
$smsConnect->shouldReceive('makeRequest')->andReturn(['err' => '11']);

Assert::exception(function() use ($smsConnect) {
	$smsConnect->sendSms('+420602111111', '');
}, 'Neogate\SmsConnect\InvalidArgumentException', 'Empty sms text');
