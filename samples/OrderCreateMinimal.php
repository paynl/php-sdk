<?php

declare(strict_types=1);

/* You might need to adjust this mapping */
require '../../../../vendor/autoload.php';

use PayNL\Sdk\Model\Request\OrderCreateRequest;
use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Config\Config;

$username = ''; // Your AT-code (AT-####-####)
$password = ''; // Your API Token
$serviceId = ''; // Your Sales location code (SL-####-####)

$config = new Config();
$config->setUsername($username);
$config->setPassword($password);

$request = new OrderCreateRequest();
$request->setServiceId($serviceId);
$request->setAmount(1.0);
$request->setReturnurl('https://yourdomain/finish.php');

try {
    $payOrder = $request->setConfig($config)->start();
} catch (PayException $e) {
    echo '<pre>';
    echo 'Technical message: ' . $e->getMessage() . PHP_EOL;
    echo 'Pay-code: ' . $e->getPayCode() . PHP_EOL;
    echo 'Customer message: ' . $e->getFriendlyMessage() . PHP_EOL;
    echo 'HTTP-code: ' . $e->getCode() . PHP_EOL;
    exit();
}

echo '<pre>';
echo 'Success, values:' . PHP_EOL;
echo 'getOrderId: ' . $payOrder->getOrderId() . PHP_EOL;
echo 'getPaymentUrl: ' . '<a target="_blank" href="' . $payOrder->getPaymentUrl() . '">' . $payOrder->getPaymentUrl() . '</a>' . PHP_EOL;
