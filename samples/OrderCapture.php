<?php

declare(strict_types=1);

/* You might need to adjust this mapping */
require '../../../../vendor/autoload.php';

use PayNL\Sdk\Model\Request\OrderCaptureRequest;
use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Config\Config;

$transactionId = $_REQUEST['pay_order_id'] ?? exit('Pass transactionId');

$orderCaptureRequest = new OrderCaptureRequest($transactionId);

$config = new Config();
$config->setUsername($_REQUEST['username'] ?? '');
$config->setPassword($_REQUEST['password'] ?? '');
$config->setCore($_REQUEST['core'] ?? '');

$orderCaptureRequest->setConfig($config);

try {
    $capture = $orderCaptureRequest->start();
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
echo 'getOrderId: ' . $capture->getId() . PHP_EOL;
echo 'getTransactionId: ' . $capture->getOrderId() . PHP_EOL;
echo 'getServiceCode: ' . $capture->getServiceCode() . PHP_EOL;
echo 'getDescription: ' . $capture->getDescription() . PHP_EOL;
echo 'getReference: ' . $capture->getReference() . PHP_EOL;
echo 'getIpAddress: ' . $capture->getIpAddress() . PHP_EOL;
echo 'getAmount getValue: ' . $capture->getAmount()->getValue() . PHP_EOL;
echo 'getAmount getCurrency: ' . $capture->getAmount()->getCurrency() . PHP_EOL;
echo 'getStatus:' . print_r($capture->getStatus(), true) . PHP_EOL;

echo 'getIntegration:' . print_r($capture->getIntegration(), true) . PHP_EOL;
echo 'getExpiresAt: ' . $capture->getExpiresAt() . PHP_EOL;
echo 'getCreatedAt: ' . $capture->getCreatedAt() . PHP_EOL;
echo 'getCreatedBy: ' . $capture->getCreatedBy() . PHP_EOL;
echo 'getModifiedAt: ' . $capture->getModifiedAt() . PHP_EOL;
echo 'getModifiedBy: ' . $capture->getModifiedBy() . PHP_EOL;
echo 'getDeletedAt: ' . $capture->getDeletedAt() . PHP_EOL;
echo 'getDeletedBy: ' . $capture->getDeletedBy() . PHP_EOL;