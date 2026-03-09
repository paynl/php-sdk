<?php

declare(strict_types=1);

/* You might need to adjust this mapping */
require '../../../../vendor/autoload.php';

use PayNL\Sdk\Model\Request\VoucherInfoRequest;
use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Config\Config;

$request = new VoucherInfoRequest();
$request->setServiceId($_REQUEST['slcode'] ?? '');

$request->setPointOfInteraction('IN_PERSON'); // ['ON_THE_MOVE', 'ECOMMERCE', 'IN_PERSON', 'INVOICE', 'DEBT_COLLECTION', 'FUNDING', 'PAYMENT_REQUEST', 'RECURRING', 'UNATTENDED', 'MOTO', 'PAYOUT']

$request->setCardNumber('12345678901234567');
$request->setPinCode('12345');

$config = new Config();
$config->setUsername($_REQUEST['username'] ?? '');
$config->setPassword($_REQUEST['password'] ?? '');
$config->setCore($_REQUEST['core'] ?? '');
$request->setConfig($config);

try {
    $response = $request->start();
} catch (PayException $e) {
    echo '<pre>';
    echo 'Technical message: ' . $e->getMessage() . PHP_EOL;
    echo 'Pay-code: ' . $e->getPayCode() . PHP_EOL;
    echo 'Customer message: ' . $e->getFriendlyMessage() . PHP_EOL;
    echo 'HTTP-code: ' . $e->getCode() . PHP_EOL;
    exit();
} catch (Exception $e) {
    echo 'Technical message: ' . $e->getMessage() . PHP_EOL;
    exit();
}

echo '<pre>';
echo 'Success, values:' . PHP_EOL;
echo 'getAmount: ' . $response->getAmount() . PHP_EOL;
echo 'getCurrency: ' . $response->getCurrency() . PHP_EOL;
echo 'expiresAt: ' . $response->getExpiresAt() . PHP_EOL;
echo 'Integration: ' . PHP_EOL;
echo ' - Test: ' . ($response->getTest() ? 'true' : 'false') . PHP_EOL;
echo ' - Point of Interaction: ' . $response->getPointOfInteraction() . PHP_EOL;
echo 'brand: ' .  PHP_EOL;
echo ' - id: ' . $response->getBrandId() . PHP_EOL;
echo ' - name: ' . $response->getBrandName() . PHP_EOL;
echo ' - iconUrl: ' . $response->getBrandIconUrl() . PHP_EOL;