<?php

declare(strict_types=1);

/* You might need to adjust this mapping */
require '../../../../vendor/autoload.php';

use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Model\Request\OrderStatusRequest;
use PayNL\Sdk\Config\Config;

$request = new OrderStatusRequest($_REQUEST['pay_order_id'] ?? '');

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
echo 'Success, values:' . PHP_EOL.PHP_EOL;

echo 'isPending: ' . ($transaction->isPending() ? 'YES' : 'no') . PHP_EOL;
echo 'isPaid: ' . ($transaction->isPaid() ? 'YES' : 'no') . PHP_EOL;
echo 'isAuthorized: ' . ($transaction->isAuthorized() ? 'YES' : 'no') . PHP_EOL;
echo 'isCancelled: ' . ($transaction->isCancelled() ? 'YES' : 'no') . PHP_EOL;
echo 'isBeingVerified: ' . ($transaction->isBeingVerified() ? 'YES' : 'no') . PHP_EOL;
echo 'isChargeBack: ' . ($transaction->isChargeBack() ? 'YES' : 'no') . PHP_EOL;
echo 'isPartialPayment: ' . ($transaction->isPartialPayment() ? 'YES' : 'no') . PHP_EOL;
echo 'isRefunded: ' . ($transaction->isRefunded() ? 'YES' : 'no') . PHP_EOL;
echo 'isPartiallyRefunded: ' . ($transaction->isPartiallyRefunded() ? 'YES' : 'no') . PHP_EOL . PHP_EOL;
echo 'getStatusCode: ' . $transaction->getStatusCode() . PHP_EOL;
echo 'getStatusName: ' . $transaction->getStatusName() . PHP_EOL;
echo 'getId: ' . $transaction->getId() . PHP_EOL;
echo 'getOrderId: ' . $transaction->getOrderId() . PHP_EOL;
echo 'getServiceCode: ' . $transaction->getServiceCode() . PHP_EOL;
echo 'getDescription: ' . $transaction->getDescription() . PHP_EOL;
echo 'getReference: ' . $transaction->getReference() . PHP_EOL;
echo 'getIpAddress: ' . $transaction->getIpAddress() . PHP_EOL.PHP_EOL;
echo 'getAmount: ' . $transaction->getAmount() . PHP_EOL;
echo 'getCurrency: ' . $transaction->getCurrency() . PHP_EOL;
echo 'paymentMethod: ' . $transaction->getPaymentMethod() . PHP_EOL;