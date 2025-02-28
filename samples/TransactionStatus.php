<?php

declare(strict_types=1);

/* You might need to adjust this mapping */
require '../../../../vendor/autoload.php';

use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Model\Request\TransactionStatusRequest;
use PayNL\Sdk\Config\Config;

$orderId = $_REQUEST['pay_order_id'] ?? exit('pay_order_id expected');

$config = new Config();
$config->setUsername($_REQUEST['username'] ?? '');
$config->setPassword($_REQUEST['password'] ?? '');

try {
    $payOrder = (new TransactionStatusRequest($orderId))->setConfig($config)->start();
} catch (PayException $e) {
    echo '<pre>';
    echo 'Technical message: ' . $e->getMessage() . PHP_EOL;
    echo 'Pay-code: ' . $e->getPayCode() . PHP_EOL;
    echo 'Customer message: ' . $e->getFriendlyMessage() . PHP_EOL;
    echo 'HTTP-code: ' . $e->getCode() . PHP_EOL;
    exit();
}

echo '<pre>';
echo 'Success, values:' . PHP_EOL . PHP_EOL;

echo 'isPending: ' . ($payOrder->isPending() ? 'YES' : 'no') . PHP_EOL;
echo 'isPaid: ' . ($payOrder->isPaid() ? 'YES' : 'no') . PHP_EOL;
echo 'isAuthorized: ' . ($payOrder->isAuthorized() ? 'YES' : 'no') . PHP_EOL;
echo 'isCancelled: ' . ($payOrder->isCancelled() ? 'YES' : 'no') . PHP_EOL;
echo 'isBeingVerified: ' . ($payOrder->isBeingVerified() ? 'YES' : 'no') . PHP_EOL;
echo 'isChargeBack: ' . ($payOrder->isChargeBack() ? 'YES' : 'no') . PHP_EOL;
echo 'isPartialPayment: ' . ($payOrder->isPartialPayment() ? 'YES' : 'no') . PHP_EOL;
echo 'isRefunded: ' . ($payOrder->isRefunded() ? 'YES' : 'no') . PHP_EOL;
echo 'isPartiallyRefunded: ' . ($payOrder->isRefundedPartial() ? 'YES' : 'no') . PHP_EOL . PHP_EOL;
echo 'getAmount: ' . ($payOrder->getAmount()) . PHP_EOL;
echo 'getAmountRefunded: ' . ($payOrder->getAmountRefunded()) . PHP_EOL . PHP_EOL;
echo 'getStatusCode: ' . $payOrder->getStatusCode() . PHP_EOL;
echo 'getStatusName: ' . $payOrder->getStatusName() . PHP_EOL;
echo 'getId: ' . $payOrder->getId() . PHP_EOL;
echo 'getOrderId: ' . $payOrder->getOrderId() . PHP_EOL;
echo 'getDescription: ' . $payOrder->getDescription() . PHP_EOL;
echo 'getReference: ' . $payOrder->getReference() . PHP_EOL;
echo 'getAmount: ' . $payOrder->getAmount() . PHP_EOL;
echo 'getCurrency: ' . $payOrder->getCurrency() . PHP_EOL;
echo 'integration: ' . ($payOrder->getIntegration()['testMode'] === true ? '1' : 0) . PHP_EOL;
echo 'expiresAt: ' . $payOrder->getExpiresAt() . PHP_EOL;
echo 'createdAt: ' . $payOrder->getCreatedAt() . PHP_EOL;
