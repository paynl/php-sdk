<?php

declare(strict_types=1);

/* You might need to adjust this mapping */
require '../../../../vendor/autoload.php';

use PayNL\Sdk\Model\Request\OrderCaptureRequest;
use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Config\Config;

$transactionId = $_REQUEST['pay_order_id'] ?? exit('pay_order_id expected');

$orderCaptureRequest = new OrderCaptureRequest($transactionId);

$productId = 'p1';
$productQuantity = 1;
$orderCaptureRequest->setProduct($productId, $productQuantity);

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
echo 'getOrderId: ' . $payOrder->getId() . PHP_EOL;
echo 'getTransactionId: ' . $payOrder->getOrderId() . PHP_EOL;
echo 'getServiceId: ' . $payOrder->getServiceId() . PHP_EOL;
echo 'getDescription: ' . $payOrder->getDescription() . PHP_EOL;
echo 'getReference: ' . $payOrder->getReference() . PHP_EOL;
echo 'getAmount getValue: ' . $payOrder->getAmount() . PHP_EOL;
echo 'getAmount getCurrency: ' . $payOrder->getCurrency() . PHP_EOL;
echo 'getStatus:' . print_r($payOrder->getStatus(), true) . PHP_EOL;
echo 'getTestmode: ' . ($payOrder->isTestmode() === true ? 'test order' : 'live order') . PHP_EOL;
echo 'getExpiresAt: ' . $payOrder->getExpiresAt() . PHP_EOL;
echo 'getCreatedAt: ' . $payOrder->getCreatedAt() . PHP_EOL;
echo 'getCreatedBy: ' . $payOrder->getCreatedBy() . PHP_EOL;
echo 'getModifiedAt: ' . $payOrder->getModifiedAt() . PHP_EOL;
echo 'getModifiedBy: ' . $payOrder->getModifiedBy() . PHP_EOL;
