<?php

declare(strict_types=1);

/* You might need to adjust this mapping */
require __DIR__ . '/../../../../vendor/autoload.php';

use GuzzleHttp\Client;
use PayNL\Sdk\Config\Config;
use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Http\GuzzleClientFactoryInterface;
use PayNL\Sdk\Model\Request\OrderStatusRequest;

class ProjectGuzzleClientFactory implements GuzzleClientFactoryInterface
{
    public function create(array $config): Client
    {
        // The SDK supplies base_uri. Timeouts are in seconds.
        $config['timeout'] = 10.0;
        $config['connect_timeout'] = 3.0;

        return new Client($config);
    }
}

$username = ''; // Your AT-code (AT-####-####)
$password = ''; // Your API Token
$orderId = ''; // The order ID to look up

$config = new Config();
$config->setUsername($username);
$config->setPassword($password);
$config->setGuzzleClientFactory(new ProjectGuzzleClientFactory());

$request = new OrderStatusRequest($orderId);

try {
    $payOrder = $request->setConfig($config)->start();
} catch (PayException $e) {
    echo '<pre>';
    echo 'Technical message: ' . $e->getMessage() . PHP_EOL;
    echo 'Pay-code: ' . $e->getPayCode() . PHP_EOL;
    echo 'Customer message: ' . $e->getFriendlyMessage() . PHP_EOL;
    echo 'HTTP-code: ' . $e->getCode() . PHP_EOL;
    exit();
}

echo '<pre>';
echo 'getOrderId: ' . $payOrder->getOrderId() . PHP_EOL;
echo 'getStatusName: ' . $payOrder->getStatusName() . PHP_EOL;
