<?php

declare(strict_types=1);

/* You might need to adjust this mapping */
require '../../../../vendor/autoload.php';

use PayNL\Sdk\Model\Request\ServiceGetConfigRequest;
use PayNL\Sdk\Config\Config;
use PayNL\Sdk\Exception\PayException;

$config = new Config();
$config->setUsername($_REQUEST['username'] ?? '');
$config->setPassword($_REQUEST['password'] ?? '');


try {
    $slCode = $_REQUEST['slcode'] ?? '';
    $config = (new ServiceGetConfigRequest($slCode))->setConfig($config)->start();
} catch (PayException $e) {
    echo '<pre>';
    echo 'Technical message: ' . $e->getMessage() . PHP_EOL;
    echo 'Pay-code: ' . $e->getPayCode() . PHP_EOL;
    echo 'Customer message: ' . $e->getFriendlyMessage() . PHP_EOL;
    echo 'HTTP-code: ' . $e->getCode() . PHP_EOL;
    exit();
}

echo '<pre>';

echo $config->getCode() . ' - ' . $config->getName() . PHP_EOL;

$banks = $config->getBanks();
print_r($banks);

$terminals = $config->getTerminals();
print_r($terminals);

$tguList = $config->getTguList();
print_r($tguList);

$paymentMethods = $config->getPaymentMethods();
foreach ($paymentMethods as $method) {
    echo $method->getId() . ' - ';
    echo $method->getName() . ' - ';
    echo $method->getImage() . ' - ';
    echo $method->getMinAmount() . ' - ';
    echo $method->getMaxAmount() . ' - ';
    echo $method->getDescription() . ' - ';
    echo $method->hasOptions() ? 'has options' : 'none';
    echo PHP_EOL;
}

foreach ($config->getCheckoutOptions() as $checkoutOption) {
    echo '=> TAG: ' . $checkoutOption->getTag() . PHP_EOL;
}