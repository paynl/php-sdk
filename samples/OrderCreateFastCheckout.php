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

$request->setReference('referenceToOrder');

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
echo 'getId: ' . $transaction->getId() . PHP_EOL;
echo 'getServiceId: ' . $transaction->getServiceId() . PHP_EOL;
echo 'getDescription: ' . $transaction->getDescription() . PHP_EOL;
echo 'getReference: ' . $transaction->getReference() . PHP_EOL;
echo 'getManualTransferCode: ' . $transaction->getManualTransferCode() . PHP_EOL;
echo 'getOrderId: ' . $transaction->getOrderId() . PHP_EOL;
echo 'getPaymentUrl: ' . '<a target="_blank" href="' . $transaction->getPaymentUrl() . '">' . $transaction->getPaymentUrl() . '</a>' . PHP_EOL;
echo 'getStatusUrl: ' . $transaction->getStatusUrl() . PHP_EOL;
echo 'getAmount value: ' . $transaction->getAmount()->getValue() . PHP_EOL;
echo 'getAmount currency: ' . $transaction->getAmount()->getCurrency() . PHP_EOL;
echo 'getUuid: ' . $transaction->getUuid() . PHP_EOL;
echo 'expiresAt: ' . $transaction->getExpiresAt() . PHP_EOL;
echo 'createdAt: ' . $transaction->getCreatedAt() . PHP_EOL;
echo 'createdBy: ' . $transaction->getCreatedBy() . PHP_EOL;
echo 'getCreatedAt: ' . $transaction->getCreatedAt() . PHP_EOL;
echo 'modifiedAt: ' . $transaction->getModifiedAt() . PHP_EOL;
echo 'modifiedBy: ' . $transaction->getModifiedBy() . PHP_EOL;
