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


// No credit
$smsConnect = Mockery::mock(SmsConnect::class)
	->makePartial()
	->shouldAllowMockingProtectedMethods();
$smsConnect->shouldReceive('makeRequest')->andReturn(['err' => '9']);

Assert::exception(function() use ($smsConnect) {
	$smsConnect->sendSms('+420602111111', '');
}, 'Neogate\SmsConnect\InvalidStateException', 'No credit');


// Too long text
$smsConnect = Mockery::mock(SmsConnect::class)
	->makePartial()
	->shouldAllowMockingProtectedMethods();
$smsConnect->shouldReceive('makeRequest')->andReturn(['err' => '12']);

Assert::exception(function() use ($smsConnect) {
	$smsConnect->sendSms('+420602111111', 'Lorem ipsum');
}, 'Neogate\SmsConnect\InvalidArgumentException', 'Text is too long, allowed maximum is 495 chars');


// Unknown error
$smsConnect = Mockery::mock(SmsConnect::class)
	->makePartial()
	->shouldAllowMockingProtectedMethods();
$smsConnect->shouldReceive('makeRequest')->andReturn(['err' => '1']);

Assert::exception(function() use ($smsConnect) {
	$smsConnect->sendSms('+420602111111', 'Lorem Ipsum');
}, 'Neogate\SmsConnect\RuntimeException', 'Unknown error');


// Invalid recipient number
$smsConnect = Mockery::mock(SmsConnect::class)
	->makePartial()
	->shouldAllowMockingProtectedMethods();
$smsConnect->shouldReceive('makeRequest')->andReturn(['err' => '10']);

Assert::exception(function() use ($smsConnect) {
	$smsConnect->sendSms('+420602111111', 'Lorem Ipsum');
}, 'Neogate\SmsConnect\InvalidArgumentException', 'Invalid recipient number');


// Database connection error
$smsConnect = Mockery::mock(SmsConnect::class)
	->makePartial()
	->shouldAllowMockingProtectedMethods();
$smsConnect->shouldReceive('makeRequest')->andReturn(['err' => '8']);

Assert::exception(function() use ($smsConnect) {
	$smsConnect->sendSms('+420602111111', 'Lorem Ipsum');
}, 'Neogate\SmsConnect\RuntimeException', 'Database connection error');


// Database connection error
$smsConnect = Mockery::mock(SmsConnect::class)
	->makePartial()
	->shouldAllowMockingProtectedMethods();
$smsConnect->shouldReceive('makeRequest')->andReturn(['err' => '5']);

Assert::exception(function() use ($smsConnect) {
	$smsConnect->sendSms('+420602111111', 'Lorem Ipsum');
}, 'Neogate\SmsConnect\InvalidStateException', 'Disallowed remote IP, see your SmsConnect setting');
