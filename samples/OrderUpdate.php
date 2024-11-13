<?php

declare(strict_types=1);

/* You might need to adjust this mapping */
require '../../../../vendor/autoload.php';

use PayNL\Sdk\Model\Request\OrderUpdateRequest;
use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Config\Config;

$transactionId = $_REQUEST['pay_order_id'] ?? exit('Pass transactionId');

$orderApproveRequest = new OrderUpdateRequest($transactionId);
$orderApproveRequest->setDescription('abc');
$orderApproveRequest->setReference('ab2711');

$config = new Config();
$config->setUsername($_REQUEST['username'] ?? '');
$config->setPassword($_REQUEST['password'] ?? '');

try {
    $payOrder = $orderApproveRequest->setConfig($config)->start();
} catch (PayException $e) {
    if ($e->getCode() == 204) {
        echo 'success';
    } else {
        echo '<pre>';
        echo 'Technical message: ' . $e->getMessage() . PHP_EOL;
        echo 'Pay-code: ' . $e->getPayCode() . PHP_EOL;
        echo 'Customer message: ' . $e->getFriendlyMessage() . PHP_EOL;
        echo 'HTTP-code: ' . $e->getCode() . PHP_EOL;
    }
    exit();
}