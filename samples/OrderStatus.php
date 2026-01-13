<?php

declare(strict_types=1);

/* You might need to adjust this mapping */
require '../../../../vendor/autoload.php';

use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Model\Request\OrderStatusRequest;
use PayNL\Sdk\Config\Config;

$request = new OrderStatusRequest($_REQUEST['pay_order_id'] ?? exit('pay_order_id expected'));

$config = new Config();
$config->setUsername($_REQUEST['username'] ?? '');
$config->setPassword($_REQUEST['password'] ?? '');
$request->setConfig($config);

try {
    $payOrder = $request->start();
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
echo 'type: ' . $payOrder->getType() . PHP_EOL;
echo 'getCustomerId: ' . $payOrder->getCustomerId() . PHP_EOL;
echo 'getCustomerName: ' . $payOrder->getCustomerName() . PHP_EOL;
echo 'isPending: ' . ($payOrder->isPending() ? 'YES' : 'no') . PHP_EOL;
echo 'isPaid: ' . ($payOrder->isPaid() ? 'YES' : 'no') . PHP_EOL;
echo 'isAuthorized: ' . ($payOrder->isAuthorized() ? 'YES' : 'no') . PHP_EOL;
echo 'isCancelled: ' . ($payOrder->isCancelled() ? 'YES' : 'no') . PHP_EOL;
echo 'isBeingVerified: ' . ($payOrder->isBeingVerified() ? 'YES' : 'no') . PHP_EOL;
echo 'isChargeBack: ' . ($payOrder->isChargeBack() ? 'YES' : 'no') . PHP_EOL;
echo 'isPartialPayment: ' . ($payOrder->isPartialPayment() ? 'YES' : 'no') . PHP_EOL;
echo 'isRefunded: ' . ($payOrder->isRefunded() ? 'YES' : 'no') . PHP_EOL;
echo 'isPartiallyRefunded: ' . ($payOrder->isRefundedPartial() ? 'YES' : 'no') . PHP_EOL . PHP_EOL;
echo 'isFastcheckout: ' . ($payOrder->isFastcheckout() ? 'YES' : 'no') . PHP_EOL;
echo 'getAmountRefunded: ' . ($payOrder->getAmountRefunded()) . PHP_EOL . PHP_EOL;
echo 'getStatusCode: ' . $payOrder->getStatusCode() . PHP_EOL;
echo 'getStatusName: ' . $payOrder->getStatusName() . PHP_EOL;
echo 'getId: ' . $payOrder->getId() . PHP_EOL;
echo 'getOrderId: ' . $payOrder->getOrderId() . PHP_EOL;
echo 'getDescription: ' . $payOrder->getDescription() . PHP_EOL;
echo 'getReference: ' . $payOrder->getReference() . PHP_EOL;
echo 'getAmount: ' . $payOrder->getAmount() . PHP_EOL;
echo 'getCurrency: ' . $payOrder->getCurrency() . PHP_EOL;
echo 'paymentMethod: ' . $payOrder->getPaymentMethod() . PHP_EOL;

print_r($payOrder->getFastCheckoutData());
