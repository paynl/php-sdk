<?php

declare(strict_types=1);

/* You might need to adjust this mapping */
require '../../../../vendor/autoload.php';

use PayNL\Sdk\Model\Product;
use PayNL\Sdk\Model\Request\OrderCreateRequest;
use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Config\Config;

$request = new OrderCreateRequest();
$request->setServiceId($_REQUEST['slcode'] ?? '');
$request->setDescription('Order ABC0123456789');
$request->setReference('SDK0123456789');
$request->setAmount((float)($_REQUEST['amount'] ?? 5.3));
$request->setCurrency('EUR');
$request->setExpire(date('Y-m-d H:i:s', strtotime('+1 DAY')));
$request->setReturnurl($_REQUEST['returnUrl'] ?? 'https://yourdomain/finish.php');
$request->setExchangeUrl($_REQUEST['exchangeUrl'] ?? 'https://yourdomain/exchange.php');
$request->setPaymentMethodId((int)($_REQUEST['paymentMethodId'] ?? 10));
$request->setIssuerId(4); # ISSUER ING
# $request->setTerminal('TH-1234-1234');
$request->setTestmode(($_REQUEST['testmode'] ?? 1) == 1);

$customer = new \PayNL\Sdk\Model\Customer();
$customer->setFirstName('John');
$customer->setLastName('Doe');
$customer->setIpAddress('92.68.12.18');
$customer->setBirthDate('1999-02-15');
$customer->setGender('M');
$customer->setPhone('0612345678');
$customer->setEmail('testbetaling@pay.nl');
$customer->setLanguage('NL');
$customer->setTrust('1');
$request->setReference('A123');

$company = new \PayNL\Sdk\Model\Company();
$company->setName('CompanyName');
$company->setCoc('12345678');
$company->setVat('NL807960147B01');
$company->setCountryCode('NL');

$customer->setCompany($company);

$request->setCustomer($customer);

$order = new \PayNL\Sdk\Model\Order();
$order->setCountryCode('NL');
$order->setDeliveryDate('2023-10-28 14:11:01');
$order->setInvoiceDate('2023-10-29 14:05:00');

$devAddress = new \PayNL\Sdk\Model\Address();
$devAddress->setCode('dev');
$devAddress->setStreetName('Istreet');
$devAddress->setStreetNumber('70');
$devAddress->setStreetNumberExtension('A');
$devAddress->setZipCode('5678CD');
$devAddress->setCity('ITest');
$devAddress->setRegionCode('ZH');
$devAddress->setCountryCode('NL');
$order->setDeliveryAddress($devAddress);

$invAddress = new \PayNL\Sdk\Model\Address();
$invAddress->setCode('inv');
$invAddress->setStreetName('Lane');
$invAddress->setStreetNumber('4');
$invAddress->setStreetNumberExtension('B1');
$invAddress->setZipCode('1234AB');
$invAddress->setCity('Test');
$invAddress->setCountryCode('NL');
$order->setInvoiceAddress($invAddress);

$products = new \PayNL\Sdk\Model\Products();

$product = new Product();
$product->setId('p1');
$product->setDescription('product1Desc');
$product->setType(Product::TYPE_ARTICLE);
$product->setAmount(1);
$product->setCurrency('EUR');
$product->setQuantity(1);
$product->setVatPercentage(0);
$products->addProduct($product);

$order->setProducts($products);

$request->setOrder($order);

$request->setStats((new \PayNL\Sdk\Model\Stats())
  ->setInfo('info')
  ->setTool('tool')
  ->setObject('object')
  ->setExtra1('ex1')
  ->setExtra2('ex2')
  ->setExtra3('ex3')
  ->setDomainId('WU-1234-1234')
);

$request->setNotification('EMAIL', 'youremail@yourdomain.ext');
$request->setTransferData([['yourField' => 'yourData'], ['tracker' => 'trackerinfo']]);

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
