<?php

declare(strict_types=1);

/* You might need to adjust this mapping */
require '../../../../vendor/autoload.php';

use PayNL\Sdk\Config\Config;
use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Model\Request\PayPartsSessionGetRequest;

$request = new PayPartsSessionGetRequest();
$request->setSessionId($_REQUEST['sessionId'] ?? exit('sessionId expected'));

$config = new Config();
$config->setUsername($_REQUEST['slcode'] ?? ''); // Sales Location code, SL-####-####.
$config->setPassword($_REQUEST['password'] ?? ''); // Secret belonging to this Sales Location.
$request->setConfig($config);

# For beta functionality
$request->useZero(($_REQUEST['useZero'] ?? '0') === '1');


try {
    $response = $request->start();
} catch (PayException $e) {
    echo '<pre>';
    echo 'Technical message: ' . htmlspecialchars($e->getMessage()) . PHP_EOL;
    echo 'Pay-code: ' . $e->getPayCode() . PHP_EOL;
    echo 'Customer message: ' . htmlspecialchars($e->getFriendlyMessage()) . PHP_EOL;
    echo 'HTTP-code: ' . $e->getCode() . PHP_EOL;
    exit();
}

echo '<pre>';
echo 'Success, values:' . PHP_EOL;
echo 'getSessionId: ' . htmlspecialchars($response->getSessionId()) . PHP_EOL;
echo 'getAmount (value in cents): ' . htmlspecialchars(json_encode($response->getAmount())) . PHP_EOL;
echo 'getCustomer: ' . htmlspecialchars(json_encode($response->getCustomer())) . PHP_EOL;
echo 'getOrder: ' . htmlspecialchars(json_encode($response->getOrder())) . PHP_EOL;
echo 'getOrderStatus: ' . htmlspecialchars(json_encode($response->getOrderStatus())) . PHP_EOL;
