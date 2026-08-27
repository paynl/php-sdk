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
        $responseResult = yourCodeToProcessRefund($payOrder);
        $responseMessage = 'Processed refund.';
    } elseif ($payOrder->isCancelled()) {
        $responseResult = yourCodeToProcessCancellation($payOrder);
        $responseMessage = 'Processed cancelled.';
    } else {
        $responseResult = true;
        $responseMessage = 'No action defined for payment state ' . $payOrder->getStatusCode();
    }
} catch (Throwable $exception) {
    error_log($exception->getMessage());

    $responseResult = false;
    $responseMessage = 'Exchange processing failed';
}

function yourCodeToProcessPendingOrder($orderId)
{
    // The same exchange call can arrive more than once. Do not process the same order twice.
    return true;
}

function yourCodeToProcessPaidOrder($orderId)
{
    // Only mark or ship the order once. If it was already processed, just return true.
    return true;
}

function yourCodeToProcessFastcheckoutOrder($fastCheckoutData)
{
    // Do not create duplicate customers or orders if this exchange call is repeated.
    return true;
}

function yourCodeToProcessRefund($payOrder)
{
    // Refund messages can also be repeated. Do not refund or register the refund twice.
    return true;
}

function yourCodeToProcessCancellation($payOrder)
{
    // Only cancel the order if it was not already paid, cancelled, or otherwise finished.
    return true;
}

$exchange->setResponse($responseResult, $responseMessage);
