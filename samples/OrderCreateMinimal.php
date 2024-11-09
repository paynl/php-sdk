<?php

declare(strict_types=1);

/* You might need to adjust this mapping */
require '../../../../vendor/autoload.php';

use PayNL\Sdk\Model\Request\OrderCreateRequest;
use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Config\Config;

$request = new OrderCreateRequest();
$request->setServiceId($_REQUEST['slcode'] ?? '');
$request->setAmount((float)($_REQUEST['amount'] ?? 5.3));
$request->setReturnurl($_REQUEST['returnUrl'] ?? 'https://yourdomain/finish.php');

$config = new Config();
$config->setUsername($_REQUEST['username'] ?? '');
$config->setPassword($_REQUEST['password'] ?? '');
$config->setCore($_REQUEST['core'] ?? '');
$request->setConfig($config);

try {
    $transaction = $request->start();
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
echo 'getPaymentUrl: ' . '<a target="_blank" href="' . $transaction->getPaymentUrl() . '">' . $transaction->getPaymentUrl() . '</a>' . PHP_EOL;
