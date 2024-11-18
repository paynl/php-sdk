<?php

declare(strict_types=1);

/* You might need to adjust this mapping */
require '../../../../vendor/autoload.php';

use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Model\Request\TerminalsBrowseRequest;
use PayNL\Sdk\Config\Config;

$config = new Config();
$config->setUsername($_REQUEST['username'] ?? '');
$config->setPassword($_REQUEST['password'] ?? '');
$config->setCore($_REQUEST['core'] ?? '');

try {
    $request = new TerminalsBrowseRequest();
    $request->setConfig($config);
    $terminalResp = $request->start();
} catch (PayException $e) {
    echo '<pre>';
    echo 'Technical message: ' . $e->getMessage() . PHP_EOL;
    echo 'Pay-code: ' . $e->getPayCode() . PHP_EOL;
    echo 'Customer message: ' . $e->getFriendlyMessage() . PHP_EOL;
    echo 'HTTP code: ' . $e->getcode();
    exit();
}

echo '<pre>';
echo 'Success, values:' . PHP_EOL . PHP_EOL;

$allTerminals = $terminalResp->getTerminals();

foreach ($allTerminals as $terminal) {
    echo str_pad($terminal->getCode(), 15, ' ');
    echo str_pad(substr($terminal->getName(), 0, 40), 40, ' ');
    echo str_pad($terminal->getAttribution(), 15, ' ');
    echo PHP_EOL;
}