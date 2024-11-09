<?php

declare(strict_types=1);

namespace PayNL\Sdk\Util;

use PayNL\Sdk\Config\Config as PayConfig;
use PayNL\Sdk\Config\Config;
use PayNL\Sdk\Model\Request\OrderStatusRequest;
use PayNL\Sdk\Model\Pay\PayStatus;
use PayNL\Sdk\Model\Pay\PayOrder;
use PayNL\Sdk\Exception\PayException;
use Exception;

/**
 * Class Signing
 *
 * @package PayNL\Sdk\Util
 */
class Exchange
{
    private array $payload;
    private ?array $custom_payload;
    private $headers;

    /**
     * @param array|null $payload
     */
    public function __construct(array $payload = null)
    {
        $this->custom_payload = $payload;
    }

    /**
     * @return bool
     */
    public function eventStateChangeToPaid()
    {
        return $this->getAction() === PayStatus::EVENT_PAID;
    }

    /**
     * Set your exchange response in the end of your exchange processing
     *
     * @param bool $result
     * @param string $message
     * @param $returnOutput If If true, then this method returs the output string
     * @return false|string|void
     */
    public function setResponse(bool $result, string $message, $returnOutput = false)
    {
        if ($this->isSignExchange() === true) {
            $response = json_encode(['result' => $result, 'description' => $message]);
        } else {
            $response = ($result === true ? 'TRUE' : 'FALSE') . '| ' . $message;
        }

        if ($returnOutput === true) {
            return $response;
        } else {
            echo $response;
            exit();
        }
    }

    /**
     * @return false|mixed|string
     */
    public function getAction()
    {
        $payload = $this->getPayload();
        return $payload['action'] ?? false;
    }

    /**
     * @return mixed|string
     */
    public function getReference()
    {
        $payload = $this->getPayload();
        return $payload['reference'] ?? '';
    }

    /**
     * @return false|mixed|string
     */
    public function getPayOrderId()
    {
        $payload = $this->getPayload();
        return $payload['payOrderId'] ?? false;
    }

    /**
     * @return array|string Array with payload or string with fault message.
     */
    public function getPayLoad()
    {
        try {
            if (!empty($this->payload)) {
                return $this->payload;
            }

            if (!empty($this->custom_payload)) {
                $request = $this->custom_payload;
            } else {
                $request = $_REQUEST ?? false;
                if ($request === false) {
                    throw new Exception('Empty payload');
                }
            }

            $action = $request['action'] ?? null;

            if (!empty($action)) {
                # The argument "action" tells us this is not coming from TGU
                $action = $request['action'] ?? null;
                $paymentProfile = $request['payment_profile_id'] ?? null;
                $payOrderId = $request['order_id'] ?? '';
                $orderId = $request['extra1'] ?? null;
                $reference = $request['extra1'] ?? null;
            } else {
                # TGU
                if (isset($request['object'])) {
                    $tguData['object'] = $request['object'];
                } else {
                    $rawBody = file_get_contents('php://input');
                    if (empty(trim($rawBody))) {
                        throw new Exception('Empty Payload');
                    }

                    $tguData = json_decode($rawBody, true, 512, 4194304);

                    $exchangeType = $tguData['type'] ?? null;
                    if ($exchangeType != 'order') {
                        throw new Exception('Cant handle exchange type other then order');
                    }
                }

                if (empty($tguData['object'])) {
                    throw new Exception('Payload error: object empty');
                }

                foreach (($tguData['object']['payments'] ?? []) as $payment) {
                    $ppid = $payment['paymentMethod']['id'] ?? null;
                }
                $paymentProfile = $ppid ?? '';
                $payOrderId = $tguData['object']['orderId'] ?? '';
                $internalStateId = (int)$tguData['object']['status']['code'] ?? 0;
                $internalStateName = $tguData['object']['status']['action'] ?? '';
                $orderId = $tguData['object']['reference'] ?? '';

                $action = in_array($internalStateId, [PayStatus::PAID, PayStatus::AUTHORIZE]) ? 'new_ppt' : $internalStateName;

                $reference = $tguData['object']['reference'] ?? '';
                $checkoutData = $tguData['object']['checkoutData'] ?? null;

                $amount = $tguData['object']['amount']['value'] ?? '';
                $amountCap = $tguData['object']['capturedAmount']['value'] ?? '';
                $amountAuth = $tguData['object']['authorizedAmount']['value'] ?? '';
            }

            $this->payload = [
                'amount' => $amount ?? null,
                'amountCap' => $amountCap ?? null,
                'amountAuth' => $amountAuth ?? null,
                'reference' => $reference,
                'action' => strtolower($action),
                'paymentProfile' => $paymentProfile ?? null,
                'payOrderId' => $payOrderId,
                'orderId' => $orderId,
                'internalStateId' => $internalStateId ?? 0,
                'internalStateName' => $internalStateName ?? null,
                'checkoutData' => $checkoutData ?? null,
                'fullPayload' => $tguData ?? $request
            ];
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $this->payload;
    }

    /**
     * Process the exchange request.
     *
     * @param Config|null $config
     * @return PayOrder
     */
    public function process(PayConfig $config = null): PayOrder
    {
        $payload = $this->getPayload();
        $payStatus = new PayStatus();
        if (!is_array($payload)) {
            throw new Exception('Invalid payload');
        }
        if (empty($config)) {
            $config = Config::getConfig();
        }

        $payOrder = new PayOrder($payload['fullPayload']);

        if ($this->isSignExchange()) {
            $signingResult = $this->checkSignExchange($config->getUsername(), $config->getPassword());
            if ($signingResult === true) {
                $paymentState = $payload['internalStateId'];
            } else {
                throw new Exception('Signing request failed');
            }
        } else {
            # Not a signing request...
            if ($payStatus->get($payload['internalStateId']) === PayStatus::PENDING) {
                $paymentState = $payload['internalStateId'];
            } else {
                # Continue to check the order status manually
                try {
                    if (empty($payload['payOrderId'])) {
                        throw new Exception('Missing pay order id in payload');
                    }
                    $request = new OrderStatusRequest($payload['payOrderId']);
                    $transaction = $request->setConfig($config)->start();
                    $paymentState = $transaction->getStatusCode();
                } catch (PayException $e) {
                    dbg($e->getMessage());
                    throw new Exception('API Retrieval error: ' . $e->getFriendlyMessage());
                }
            }
        }

        $payOrder->setAmount($payload['amount']);
        $payOrder->setPaymentProfileId($payload['paymentProfile']);
        $payOrder->setOrderId($payload['payOrderId']);
        $payOrder->setReference($payload['reference'] ?? '');
        $payOrder->setStateId($payStatus->get($paymentState));

        return $payOrder;
    }

    /**
     * @param string $username Token code
     * @param string $password API Token
     * @return bool Returns true if the signing is successful and authorised
     */
    public function checkSignExchange(string $username = '', string $password = ''): bool
    {
        try {
            if (!$this->isSignExchange()) {
                throw new Exception('No signing exchange');
            }

            if (empty($username) || empty($password)) {
                $config = Config::getConfig();
                $username = (string)$config->getUsername();
                $password = (string)$config->getPassword();
            }

            $headers = $this->getRequestHeaders();
            $tokenCode = trim($headers['signature-keyid'] ?? '');

            if (empty($tokenCode)) {
                throw new Exception('TokenCode empty');
            }
            if ($tokenCode !== $username) {
                throw new Exception('TokenCode invalid');
            }
            $rawBody = file_get_contents('php://input');
            $signature = hash_hmac($headers['signature-algorithm'] ?? 'sha256', $rawBody, $password);

            if (!hash_equals($headers['signature'] ?? '', $signature)) {
                throw new Exception('Signature failed');
            }
        } catch (Exception $e) {
            dbg('checkSignExchange: ' . $e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * @return bool
     */
    public function isSignExchange(): bool
    {
        $headers = $this->getRequestHeaders();
        $signingMethod = $headers['signature-method'] ?? null;
        return $signingMethod === 'HMAC';
    }

    /**
     * @return array|false|string
     */
    private function getRequestHeaders()
    {
        if (empty($this->headers)) {
            $this->headers = array_change_key_case(getallheaders());
        }
        return $this->headers;
    }

}