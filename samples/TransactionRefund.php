<?php

declare(strict_types=1);

/* You might need to adjust this mapping */
require '../../../../vendor/autoload.php';

use PayNL\Sdk\Model\Request\TransactionRefundRequest;
use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Config\Config;

$transactionId = $_REQUEST['pay_order_id'] ?? exit('expected pay_order_id');

$transactionRefundRequest = new TransactionRefundRequest($transactionId);

# $transactionRefundRequest->addProduct('p1',1);
# $transactionRefundRequest->addProduct('p2',2);
# $transactionRefundRequest->setAmount(5.3);
# $transactionRefundRequest->setDescription('Item returned');
# $transactionRefundRequest->setProcessDate('2024-10-06 10:12:12');
# $transactionRefundRequest->setVatPercentage(21);
# $transactionRefundRequest->setExchangeUrl('https://pay.nl/exchange.php');

$config = new Config();
$config->setUsername($_REQUEST['username'] ?? '');
$config->setPassword($_REQUEST['password'] ?? '');
$transactionRefundRequest->setConfig($config);

try {
    $refund = $transactionRefundRequest->start();
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
echo 'getOrderId: ' . $refund->getOrderId() . PHP_EOL;
echo 'getTransactionId: ' . $refund->getTransactionId() . PHP_EOL;
echo 'getDescription: ' . $refund->getDescription() . PHP_EOL;
echo 'getProcessDate: ' . $refund->getProcessDate() . PHP_EOL;
echo 'getAmount getValue: ' . $refund->getAmount()->getValue() . PHP_EOL;
echo 'getAmount getCurrency: ' . $refund->getAmount()->getCurrency() . PHP_EOL;
echo 'getAmountRefunded getValue: ' . $refund->getAmountRefunded()->getValue() . PHP_EOL;
echo 'getAmountRefunded getCurrency: ' . $refund->getAmountRefunded()->getCurrency() . PHP_EOL;
echo 'getRefundedTransactions : ' . PHP_EOL;
print_r($refund->getRefundedTransactions());
echo 'getCreatedAt: ' . $refund->getCreatedAt() . PHP_EOL;
echo 'exgetCreatedBypiresAt: ' . $refund->getCreatedBy() . PHP_EOL;