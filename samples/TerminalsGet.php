<?php

declare(strict_types=1);

/* You might need to adjust this mapping */
require '../../../../vendor/autoload.php';

use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Model\Request\TerminalsGetRequest;
use PayNL\Sdk\Config\Config;

$terminalCode = $_REQUEST['terminalcode'] ?? exit('terminalcode expected');
$TerminalGetRequest = new TerminalsGetRequest($terminalCode);

$config = new Config();
$config->setUsername($_REQUEST['username'] ?? '');
$config->setPassword($_REQUEST['password'] ?? '');
$config->setCore($_REQUEST['core'] ?? '');

try {
    $TerminalGetRequest->setConfig($config);
    $terminal = $TerminalGetRequest->start();
} catch (PayException $e) {
    echo '<pre>';
    echo 'Technical message: ' . $e->getMessage() . PHP_EOL;
    echo 'Pay-code: ' . $e->getPayCode() . PHP_EOL;
    echo 'Customer message: ' . $e->getFriendlyMessage() . PHP_EOL;
    echo 'HTTP-code: ' . $e->getCode() . PHP_EOL;
    exit();
}

echo '<pre>';
echo 'Success, values:' . PHP_EOL . PHP_EOL;
echo 'Code: ' . $terminal->getCode() . PHP_EOL;
echo 'Name: ' . $terminal->getName() . PHP_EOL;
echo 'TerminalType: ' . $terminal->getTerminalType() . PHP_EOL;
echo 'getContractStartDate: ' . $terminal->getContractStartDate() . PHP_EOL;
