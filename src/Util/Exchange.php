<?php

declare(strict_types=1);

namespace PayNL\Sdk\Util;

use PayNL\Sdk\Config\Config as PayConfig;
use PayNL\Sdk\Config\Config;
use PayNL\Sdk\Model\Amount;
use PayNL\Sdk\Model\Request\OrderStatusRequest;
use PayNL\Sdk\Model\Pay\PayStatus;
use PayNL\Sdk\Model\Pay\PayOrder;
use PayNL\Sdk\Model\Pay\PayLoad;
use PayNL\Sdk\Exception\PayException;
use Exception;
use PayNL\Sdk\Model\Request\TransactionStatusRequest;

/**
 * Class Signing
 *
 * @package PayNL\Sdk\Util
 */
class Exchange
{
    private PayLoad $payload;
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
     * @param bool $returnOutput If true, then this method returs the output string
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
     * @return string
     */
    public function getAction()
    {
        try {
            $payload = $this->getPayload();
        } catch (\Throwable $e) {
            return false;
        }
        return $payload->getAction();
    }

    /**
     * @return mixed|string
     */
    public function getReference()
    {
        try {
            $payload = $this->getPayload();
        } catch (\Throwable $e) {
            return false;
        }
        return $payload->getReference();
    }

    /**
     * @return string
     */
    public function getPayOrderId()
    {
        try {
            $payload = $this->getPayload();
        } catch (\Throwable $e) {
            return false;
        }
        return $payload->getPayOrderId();
    }

    /**
     * @return PayLoad
     * @throws Exception
     */
    public function getPayLoad()
    {
        if (!empty($this->payload)) {
            # Payload already initilized, then return payload.
            return $this->payload;
        }

        if (!empty($this->custom_payload)) {
            # In case a payload has been provided, use that one.
            $request = $this->custom_payload;
        } else {
            $request = $_REQUEST ?? false;
            if ($request === false) {
                throw new Exception('Empty payload', 8001);
            }
        }

        $action = $request['action'] ?? null;

        if (!empty($action)) {
            # The argument "action" tells us this is GMS
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
                    throw new Exception('Empty payload', 8002);
                }

                $tguData = json_decode($rawBody, true, 512, 4194304);

                $exchangeType = $tguData['type'] ?? null;
                if ($exchangeType != 'order') {
                    throw new Exception('Cant handle exchange type other then order', 8003);
                }
            }

            if (empty($tguData['object'])) {
                throw new Exception('Payload error: object empty', 8004);
            }

            foreach (($tguData['object']['payments'] ?? []) as $payment) {
                $ppid = $payment['paymentMethod']['id'] ?? null;
            }
            $paymentProfile = $ppid ?? '';
            $type = $tguData['object']['type'] ?? '';
            $payOrderId = $tguData['object']['orderId'] ?? '';
            $internalStateId = (int)$tguData['object']['status']['code'] ?? 0;
            $internalStateName = $tguData['object']['status']['action'] ?? '';
            $orderId = $tguData['object']['reference'] ?? '';

            $action = in_array($internalStateId, [PayStatus::PAID, PayStatus::AUTHORIZE]) ? 'new_ppt' : $internalStateName;

            $reference = $tguData['object']['reference'] ?? '';
            $checkoutData = $tguData['object']['checkoutData'] ?? null;

            $amount = $tguData['object']['amount']['value'] ?? '';
            $currency = $tguData['object']['amount']['currency'] ?? '';
            $amountCap = $tguData['object']['capturedAmount']['value'] ?? '';
            $amountAuth = $tguData['object']['authorizedAmount']['value'] ?? '';
        }

        $this->payload = new PayLoad([
            'type' => $type ?? '',
            'amount' => $amount ?? null,
            'currency' => $currency ?? '',
            'amount_cap' => $amountCap ?? null,
            'amount_auth' => $amountAuth ?? null,
            'reference' => $reference,
            'action' => strtolower($action),
            'payment_profile' => $paymentProfile ?? null,
            'pay_order_id' => $payOrderId,
            'order_id' => $orderId,
            'internal_state_id' => $internalStateId ?? 0,
            'internal_state_name' => $internalStateName ?? null,
            'checkout_data' => $checkoutData ?? null,
            'full_payload' => $tguData ?? $request
        ]);

        return $this->payload;
    }

    /**
     * Process the exchange request.
     *
     * @param Config|null $config
     * @return PayOrder
     * @throws Exception
     */
    public function process(PayConfig $config = null): PayOrder
    {
        $payload = $this->getPayload();

        if (empty($config)) {
            $config = Config::getConfig();
        }

        if ($this->isSignExchange()) {
            $signingResult = $this->checkSignExchange($config->getUsername(), $config->getPassword());

            if ($signingResult === true) {
                dbg('signingResult true');
                $payOrder = new PayOrder($payload->getFullPayLoad());
                $payOrder->setReference($payload->getReference());
                $payOrder->setOrderId($payload->getPayOrderId());
                $payOrder->setAmount(new Amount($payload->getAmount(), $payload->getCurrency()));
                $payOrder->setType($payload->getType());
            } else {
                throw new Exception('Signing request failed');
            }
        } else {
            try {
                $payloadState = (new PayStatus())->get($payload->getInternalStateId());
            } catch (\Throwable $e) {
                $payloadState = null;
            }

            # Not a signing request...
            if ($payloadState === PayStatus::PENDING) {
                $payOrder = new PayOrder();
                $payOrder->setStatusCodeName(PayStatus::PENDING, 'PENDING');
            } else {
                # Continue to check the order status manually
                try {
                    if (empty($payload->getPayOrderId())) {
                        throw new Exception('Missing pay order id in payload');
                    }

                    $action = $this->getAction();
                    if (stripos($action, 'refund') !== false) {
                        dbg('TransactionStatusRequest');
                        $request = new TransactionStatusRequest($payload->getPayOrderId());
                    } else {
                        dbg('OrderStatusRequest');
                        $request = new OrderStatusRequest($payload->getPayOrderId());
                    }

                    $payOrder = $request->setConfig($config)->start();

                } catch (PayException $e) {
                    dbg($e->getMessage());
                    throw new Exception('API Retrieval error: ' . $e->getFriendlyMessage());
                }
            }
        }

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