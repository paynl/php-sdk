<?php

declare(strict_types=1);

/* You might need to adjust this mapping */
require '../../../../vendor/autoload.php';

use PayNL\Sdk\Model\Request\OrderCaptureRequest;
use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Config\Config;

$transactionId = $_REQUEST['pay_order_id'] ?? exit('pay_order_id expected');

$orderCaptureRequest = new OrderCaptureRequest($transactionId);

$config = new Config();
$config->setUsername($_REQUEST['username'] ?? '');
$config->setPassword($_REQUEST['password'] ?? '');
$config->setCore($_REQUEST['core'] ?? '');

$orderCaptureRequest->setConfig($config);

try {
    $payOrder = $orderCaptureRequest->start();
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
echo 'getDescription: ' . $payOrder->getDescription() . PHP_EOL;
echo 'getAmount: ' . $payOrder->getAmount() . PHP_EOL;
