<?php

declare(strict_types=1);

/* You might need to adjust this mapping */
require '../../../../vendor/autoload.php';

use PayNL\Sdk\Config\Config;
use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Model\Address;
use PayNL\Sdk\Model\Amount;
use PayNL\Sdk\Model\Customer;
use PayNL\Sdk\Model\Order;
use PayNL\Sdk\Model\Product;
use PayNL\Sdk\Model\Request\PayPartsSessionCreateRequest;
use PayNL\Sdk\Serialization\JsonMapper;

$request = new PayPartsSessionCreateRequest();
$request->setDescription('Order ABC0123456789');
$request->setAmount((float)($_REQUEST['amount'] ?? 5.30))->setCurrency('EUR');

# Or use an Amount object containing cents:
# $request->setAmount(Amount::fromCents(530, 'EUR'));

$request->setExpire(date('c', strtotime('+1 hour')));
$request->setReturnUrl($_REQUEST['returnUrl'] ?? 'https://yourdomain/finish.php');
$request->setExchangeUrl($_REQUEST['exchangeUrl'] ?? 'https://yourdomain/exchange.php');
$request->setIntegration(['checkoutUrl' => 'https://yourdomain/checkout']);

$customer = new Customer();
$customer->setFirstName('John');
$customer->setLastName('Doe');
$customer->setEmail('testbetaling@pay.nl');
$customer->setPhone('0612345678');
$customer->setLocale('nl-NL');
$request->setCustomer($customer);

$deliveryAddress = new Address();
$deliveryAddress->setFirstName('John');
$deliveryAddress->setLastName('Doe');
$deliveryAddress->setStreetName('Teststraat');
$deliveryAddress->setStreetNumber('70');
$deliveryAddress->setStreetNumberExtension('A');
$deliveryAddress->setZipCode('5678CD');
$deliveryAddress->setCity('Amsterdam');
$deliveryAddress->setRegionCode('Noord-Holland');
$deliveryAddress->setCountryCode('NL');

$invoiceAddress = clone $deliveryAddress;

$product = new Product();
$product->setId('p1');
$product->setDescription('Example product');
$product->setType(Product::TYPE_ARTICLE);
$product->setAmount((float)($_REQUEST['amount'] ?? 5.30));
$product->setCurrency('EUR');
$product->setQuantity(1);
$product->setVatPercentage(21);

# Include customer, addresses and products when offering redirect methods such as iDEAL.
# Add shipping as a Product with Product::TYPE_SHIPPING and its own VAT percentage.
# The sum of the product prices multiplied by their quantities should match the session amount.
$order = new Order();
$order->setCountryCode('NL');
$order->setDeliveryAddress($deliveryAddress);
$order->setInvoiceAddress($invoiceAddress);
$order->addProduct($product);
$request->setOrder($order);

$config = new Config();
# PayParts uses the Sales Location code and its secret, rather than an AT-code/API token.
$config->setUsername($_REQUEST['slcode'] ?? '');
$config->setPassword($_REQUEST['password'] ?? '');
$request->setConfig($config);
$request->useZero(($_REQUEST['useZero'] ?? '0') === '1');
# For pre-production: $request->useZero(); // Independent of the core setting.

try {
    $request->setReference('SDK0123456789');
    $response = $request->start();
} catch (PayException $e) {
    echo '<pre>';
    echo 'Technical message: ' . htmlspecialchars($e->getMessage()) . PHP_EOL;
    echo 'Pay-code: ' . $e->getPayCode() . PHP_EOL;
    echo 'Customer message: ' . htmlspecialchars($e->getFriendlyMessage()) . PHP_EOL;
    echo 'HTTP-code: ' . $e->getCode() . PHP_EOL;
    exit();
} catch (Exception $e) {
    echo 'Technical message: ' . htmlspecialchars($e->getMessage()) . PHP_EOL;
    exit();
}

echo '<pre>';
echo 'Success, values:' . PHP_EOL;
echo 'getSessionId: ' . htmlspecialchars($response->getSessionId()) . PHP_EOL;
echo 'getSessionToken: ' . htmlspecialchars($response->getSessionToken()) . PHP_EOL;

$responseData = json_decode((new JsonMapper())->encode($response), true, 512, JSON_THROW_ON_ERROR);
echo PHP_EOL . 'JSON decoded response:' . PHP_EOL;
echo htmlspecialchars(print_r($responseData, true));