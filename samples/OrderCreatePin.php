<?php

declare(strict_types=1);

/* You might need to adjust this mapping */
require '../../../../vendor/autoload.php';

use PayNL\Sdk\Model\Product;
use PayNL\Sdk\Model\Request\OrderCreateRequest;
use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Config\Config;

$request = new OrderCreateRequest();
$request->setServiceId( $_REQUEST['slcode'] ?? '');
$request->setDescription('Order ABC0123456789');
$request->setReference('ABC0123456789');
$request->setReturnurl('https://yourdomain/finish.php');
$request->setExchangeUrl('https://yourdomain/exchange.php');
$request->setAmount(0.01);
$request->setCurrency('EUR');
$request->setPaymentMethodId(1927); # PIN
$request->setTerminal($_REQUEST['terminalcode'] ?? '');
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
$customer->setReference('MyRef');

$company = new \PayNL\Sdk\Model\Company();
$company->setName('CompanyName');
$company->setCoc('12345678');
$company->setVat('NL807960147B01');
$company->setCountryCode('NL');

$customer->setCompany($company);

$request->setCustomer($customer);

$order = new \PayNL\Sdk\Model\Order();
$order->setCountryCode('NL');
$order->setDeliveryDate('2024-12-28 14:11:01');
$order->setInvoiceDate('2024-12-29 14:05:00');

$devAddress = new \PayNL\Sdk\Model\Address();
$devAddress->setCode('dev');
$devAddress->setStreetName('Istreet');
$devAddress->setStreetNumber('70');
$devAddress->setStreetNumberExtension('A');
$devAddress->setZipCode('5678CD');
$devAddress->setCity('ITest');
$devAddress->setCountryCode('NL');
$order->setDeliveryAddress($devAddress);

$invAddress = new \PayNL\Sdk\Model\Address();
$invAddress->setCode('inv');
$invAddress->setStreetName('Lane');
$invAddress->setStreetNumber('4');
$invAddress->setStreetNumberExtension('B1');
$invAddress->setZipCode('1234AB');
$invAddress->setCity('Test');
$invAddress->setCountryCode('BE');
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

$config = new Config();
$config->setUsername($_REQUEST['username'] ?? '');
$config->setPassword($_REQUEST['password'] ?? '');
$config->setCore($_REQUEST['core'] ?? '');
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
echo 'Success, values:' . PHP_EOL;
echo 'getId: ' . $payOrder->getId() . PHP_EOL;
echo 'getPaymentUrl: ' . '<a target="_blank" href="' . $payOrder->getPaymentUrl() . '">' . $payOrder->getPaymentUrl() . '</a>' . PHP_EOL;