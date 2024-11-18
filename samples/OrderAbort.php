<?php

declare(strict_types=1);

/* You might need to adjust this mapping */
require '../../../../vendor/autoload.php';

use PayNL\Sdk\Model\Request\OrderAbortRequest;
use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Config\Config;

$transactionId = $_REQUEST['pay_order_id'] ?? exit('pay_order_id expected');

$orderAbortRequest = new OrderAbortRequest($transactionId);

$config = new Config();
$config->setUsername($_REQUEST['username'] ?? '');
$config->setPassword($_REQUEST['password'] ?? '');
$config->setCore($_REQUEST['core'] ?? '');
$orderAbortRequest->setConfig($config);

try {
    $transaction = $orderAbortRequest->start();
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
echo 'getOrderId: ' . $transaction->getId() . PHP_EOL;
echo 'getTransactionId: ' . $transaction->getOrderId() . PHP_EOL;
echo 'getDescription: ' . $transaction->getDescription() . PHP_EOL;
echo 'getReference: ' . $transaction->getReference() . PHP_EOL;
echo 'getAmount getValue: ' . $transaction->getAmount() . PHP_EOL;
echo 'getAmount getCurrency: ' . $transaction->getAmount() . PHP_EOL;
echo 'getAuthorizedAmount getCurrency: ' . $transaction->getAuthorizedAmount()->getCurrency() . PHP_EOL;
echo 'getAuthorizedAmount getValue: ' . $transaction->getAuthorizedAmount()->getValue() . PHP_EOL;
echo 'getStatus:' . print_r($transaction->getStatus(), true) . PHP_EOL;
echo 'getPaymentData:' . print_r($transaction->getPayments(), true) . PHP_EOL;
echo 'getIntegration:' . print_r($transaction->getIntegration(), true) . PHP_EOL;
echo 'getExpiresAt: ' . $transaction->getExpiresAt() . PHP_EOL;
echo 'getCreatedAt: ' . $transaction->getCreatedAt() . PHP_EOL;
echo 'getCreatedBy: ' . $transaction->getCreatedBy() . PHP_EOL;
echo 'getModifiedAt: ' . $transaction->getModifiedAt() . PHP_EOL;
echo 'getModifiedBy: ' . $transaction->getModifiedBy() . PHP_EOL;
