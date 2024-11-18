<?php

# This is a minimal example on how to handle a Pay. exchange call and process an order
declare(strict_types=1);

# You might need to adjust this mapping for your implementation
require '../../../../vendor/autoload.php';

use PayNL\Sdk\Util\Exchange;

$exchange = new Exchange();

try {
    # Process the exchange request
    $payOrder = $exchange->process();

    if ($payOrder->isPending()) {
        $responseResult = yourCodeToProcessPendingOrder($payOrder->getReference());
        $responseMessage = 'Processed pending';

    } elseif ($payOrder->isPaid() || $payOrder->isAuthorized()) {
        if ($payOrder->isFastCheckout()) {
            $data = $payOrder->getFastCheckoutData();
            $responseResult = yourCodeToProcessFastcheckoutOrder($data);
            $responseMessage = 'Processed fastcheckout paid. Order: ' . $payOrder->getReference();
        } else {
            $responseResult = yourCodeToProcessPaidOrder($payOrder->getReference());
            $responseMessage = 'Processed paid. Order: ' . $payOrder->getReference();
        }
    } elseif ($payOrder->isRefunded()) {
        $responseResult = true; # Your code to process refund here
        $responseMessage = 'Processed refund.';
    } elseif ($payOrder->isCancelled()) {
        $responseResult = true; # Your code to cancel order here
        $responseMessage = 'Processed cancelled.';
    } else {
        $responseResult = true;
        $responseMessage = 'No action defined for payment state ' . $payOrder->getStatusCode();
    }
} catch (Throwable $exception) {
    $responseResult = false;
    $responseMessage = $exception->getMessage();
}

function yourCodeToProcessPendingOrder($orderId)
{
    return true;
}

function yourCodeToProcessPaidOrder($orderId)
{
    return true;
}

function yourCodeToProcessFastcheckoutOrder($orderId)
{
    return true;
}

$exchange->setResponse($responseResult, $responseMessage);
