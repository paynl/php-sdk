<?php

declare(strict_types=1);

/* You might need to adjust this mapping */
require '../../../../vendor/autoload.php';

use PayNL\Sdk\Model\Request\OrderCreateRequest;
use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Config\Config;

$request = new OrderCreateRequest();
$request->setServiceId($_REQUEST['slcode'] ?? '');

$request->setPaymentMethodId((int)($_REQUEST['paymentMethodId'] ?? 10));
$request->setTestmode(($_REQUEST['testmode'] ?? 1) == 1);
$request->setAmount((float)($_REQUEST['amount'] ?? 5.3));
$request->setReturnurl($_REQUEST['returnUrl'] ?? 'https://yourdomain/finish.php');
$request->setExchangeUrl($_REQUEST['exchangeUrl'] ?? 'https://yourdomain/exchange.php');

$request->enableFastCheckout();

$request->setStats((new \PayNL\Sdk\Model\Stats())
  ->setInfo('info')
  ->setTool('tool')
  ->setObject('object')
  ->setExtra1('ex1')
  ->setExtra2('ex2')
  ->setExtra3('ex3')
  ->setDomainId('WU-1234-1234')
);

$config = new Config();
$config->setUsername($_REQUEST['username'] ?? '');
$config->setPassword($_REQUEST['password'] ?? '');
$config->setCore($_REQUEST['core'] ?? '');
$request->setConfig($config);

try {
    $request->setReference('referenceToOrder');
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
echo 'Success, values:' . PHP_EOL;
echo 'getId: ' . $payOrder->getId() . PHP_EOL;
echo 'getServiceId: ' . $payOrder->getServiceId() . PHP_EOL;
echo 'getDescription: ' . $payOrder->getDescription() . PHP_EOL;
echo 'getReference: ' . $payOrder->getReference() . PHP_EOL;
echo 'getManualTransferCode: ' . $payOrder->getManualTransferCode() . PHP_EOL;
echo 'getOrderId: ' . $payOrder->getOrderId() . PHP_EOL;
echo 'getPaymentUrl: ' . '<a target="_blank" href="' . $payOrder->getPaymentUrl() . '">' . $payOrder->getPaymentUrl() . '</a>' . PHP_EOL;
echo 'getStatusUrl: ' . $payOrder->getStatusUrl() . PHP_EOL;
echo 'getAmount value: ' . $payOrder->getAmount() . PHP_EOL;
echo 'getAmount currency: ' . $payOrder->getAmount() . PHP_EOL;
echo 'getUuid: ' . $payOrder->getUuid() . PHP_EOL;
echo 'expiresAt: ' . $payOrder->getExpiresAt() . PHP_EOL;
echo 'createdAt: ' . $payOrder->getCreatedAt() . PHP_EOL;
echo 'createdBy: ' . $payOrder->getCreatedBy() . PHP_EOL;
echo 'getCreatedAt: ' . $payOrder->getCreatedAt() . PHP_EOL;
echo 'modifiedAt: ' . $payOrder->getModifiedAt() . PHP_EOL;
echo 'modifiedBy: ' . $payOrder->getModifiedBy() . PHP_EOL;
