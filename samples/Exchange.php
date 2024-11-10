<?php

# This is a minimal example on how to handle a Pay. exchange call and process an order
declare(strict_types=1);

# You might need to adjust this mapping for your situation
require '../../../../vendor/autoload.php';

use PayNL\Sdk\Util\Exchange;
use PayNL\Sdk\Model\Pay\PayStatus;

$exchange = new Exchange();


try {
    # Process the exchange request
    $payOrder = $exchange->process();

    switch ($payOrder->getStateId()) {
        case PayStatus::PENDING:
            $responseResult = yourCodeToProcessPendingOrder($payOrder->getReference());
            $responseMessage = 'Processed pending';
            break;
        case PayStatus::PAID:
            $responseResult = yourCodeToProcessPaidOrder($payOrder->getReference());
            $responseMessage = 'Processed paid. Order: ' . $payOrder->getReference();
            break;
        default :
            $responseResult = true;
            $responseMessage = 'No action defined for payment state ' . $payOrder->getStateId();
    }
} catch (Throwable $exception) {
    $responseResult = false;
    $responseMessage = $exception->getMessage();
}

/**
 * @param $orderId
 * @return true
 */
function yourCodeToProcessPendingOrder($orderId) {
    return true;
}

/**
 * @param $orderId
 * @return true
 */
function yourCodeToProcessPaidOrder($orderId)
{
    return true;
}

$exchange->setResponse($responseResult, $responseMessage);
