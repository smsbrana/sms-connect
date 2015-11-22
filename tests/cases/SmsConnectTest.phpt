<?php

use Tester\Assert;

$container = require __DIR__ . '/../bootstrap.php';

// empty auth
Assert::exception(function() {
    new \Neogate\SmsConnect\SmsConnect(NULL, NULL);
}, 'InvalidArgumentException', 'Empty login');
