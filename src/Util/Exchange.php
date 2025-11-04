<?php

declare(strict_types=1);

namespace PayNL\Sdk\Util;

use PayNL\Sdk\Config\Config;
use PayNL\Sdk\Model\Amount;
use PayNL\Sdk\Util\ExchangeResponse;
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
    private mixed $headers;
    private string $gmsReferenceKey = 'extra1';

    /**
     * @return string
     */
    public function getGmsReferenceKey(): string
    {
        return $this->gmsReferenceKey;
    }

    /**
     * Specifies the field to use for order retrieval when older exchange types, such as refunds, provide the order ID in a non-standard field(extra1).
     * @param $key
     * @return $this
     */
    public function setGmsReferenceKey($key): self
    {
        $this->gmsReferenceKey = $key;
        return $this;
    }

    /**
     * @param array|null $payload
     */
    public function __construct(?array $payload = null)
    {
        $this->custom_payload = $payload;
    }

    /**
     * @param boolean $includeAuth If yes, treat authorize as "paid"
     * @return boolean
     * @throws PayException
     */
    public function eventPaid(bool $includeAuth = false): bool
    {
        return $this->getAction() === PayStatus::EVENT_PAID || ($includeAuth == true && $this->getAction() === PayStatus::AUTHORIZE);
    }

    /**
     * @return boolean
     * @throws PayException
     */
    public function eventChargeback(): bool
    {
        return substr($this->getAction(), 0, 10) === PayStatus::EVENT_CHARGEBACK;
    }

    /**
     * @return boolean
     * @throws PayException
     */
    public function eventRefund()
    {
        return substr($this->getAction(), 0, 6) === PayStatus::EVENT_REFUND;
    }

    /**
     * @return boolean
     * @throws PayException
     */
    public function eventCapture()
    {
        return $this->getAction() == PayStatus::EVENT_CAPTURE;
    }

    /**
     * Set your exchange response in the end of your exchange processing
     *
     * @param boolean $result
     * @param string $message
     * @param boolean $returnOutput
     * @return false|string|void
     */
    public function setResponse(bool $result, string $message, bool $returnOutput = false)
    {
        $message = ucfirst(strtolower($message));

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
     * @param \PayNL\Sdk\Util\ExchangeResponse $e
     * @param boolean $returnOutput
     * @return false|string|null
     */
    public function setExchangeResponse(ExchangeResponse $e, bool $returnOutput = false)
    {
        return $this->setResponse($e->getResult(), $e->getMessage(), $returnOutput);
    }


    /**
     * Retrieve payload with exception handling.
     *
     * @return Payload
     * @throws PayException
     */
    private function getSafePayload(): Payload
    {
        try {
            return $this->getPayload();
        } catch (\Throwable $e) {
            throw new PayException('Could not retrieve payload: ' . $e->getMessage(), 0, 0);
        }
    }
    /**
     * @return bool
     */
    public function isFastCheckout(): bool
    {
        return $this->getSafePayload()->isFastCheckout();
    }

    /**
     * @return string
     * @throws PayException
     */
    public function getAction(): string
    {
        return strtolower($this->getSafePayload()->getAction());
    }

    /**
     * @return string
     * @throws PayException
     */
    public function getReference(): string
    {
        return $this->getSafePayload()->getReference();
    }

    /**
     * @return string
     * @throws PayException
     */
    public function getPayOrderId(): string
    {
        return $this->getSafePayload()->getPayOrderId();
    }


    /**
     * @param array $request
     * @return array
     */
    private function legacyReturn(array $request)
    {
        $action = $request['action'] ?? null;
        $paymentProfile = $request['payment_profile_id'] ?? null;
        $payOrderId = $request['order_id'] ?? '';
        $orderId = $request['extra1'] ?? null;
        $reference = $request[$this->getGmsReferenceKey()] ?? null;

        return [$action, $paymentProfile, $payOrderId, $orderId, $reference];
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
            [$action, $paymentProfile, $payOrderId, $orderId, $reference] = $this->legacyReturn($request);

        } else {
            # TGU
            if (isset($request['object'])) {
                $tguData['object'] = $request['object'];
            } else {
                $rawBody = file_get_contents('php://input');
                if (empty(trim($rawBody))) {
                    throw new Exception('Empty or incomplete payload', 8002);
                }
                $tguData = json_decode($rawBody, true, 512, JSON_BIGINT_AS_STRING);
            }

            if (empty($tguData['object'])) {
                throw new Exception('Payload error: object empty', 8004);
            }

            if (!isset($tguData['type']) && isset($tguData['action'])) {
                # Legacy call,
                [$action, $paymentProfile, $payOrderId, $orderId, $reference] = $this->legacyReturn($tguData);
            } else {
                foreach (($tguData['object']['payments'] ?? []) as $payment) {
                    $ppid = $payment['paymentMethod']['id'] ?? null;
                }
                $paymentProfile = $ppid ?? '';
                $type = $tguData['object']['type'] ?? '';
                $payOrderId = $tguData['object']['orderId'] ?? '';
                $internalStateId = (int)($tguData['object']['status']['code'] ?? 0);

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
     * Processes the exchange request and returns a PayOrder object with the correct payment state.
     *
     * @param Config|null $config Optional configuration object. If not provided, default config is used.
     * @return PayOrder
     * @throws Exception If signing fails or order status cannot be retrieved
     */
    public function process(?Config $config = null): PayOrder
    {
        $payload = $this->getPayload();

        if (empty($config)) {
            $config = Config::getConfig();
        }

        if (empty($config->getUsername()) || empty($config->getPassword())) {
            throw new Exception('Process failed, config not set', 8003);
        }

        if ($this->isSignExchange()) {
            $signingResult = $this->checkSignExchange($config->getUsername(), $config->getPassword());

            if ($signingResult === true) {
                paydbg('signingResult true');
                $payOrder = new PayOrder($payload->getFullPayLoad());
                $payOrder->setReference($payload->getReference());
                $payOrder->setOrderId($payload->getPayOrderId());
                $payOrder->setAmount(new Amount($payload->getAmount(), $payload->getCurrency()));
                $payOrder->setType($payload->getType());
            } else {
                throw new Exception('Signing request failed');
            }

            # Return with correct status code; otherwise, proceed with API call to retrieve status.
            if ($payOrder->getStatusCode() != 0) {
                return $payOrder;
            }
        }

        # Continue to retrieve the order status with API call..

        if ($this->getPayloadState($payload) === PayStatus::PENDING) {
            $payOrder = new PayOrder();
            $payOrder->setType($payload->getType());
            $payOrder->setStatusCodeName(PayStatus::PENDING, 'PENDING');
        } else {
            # Continue to check the order status manually
            try {
                if (empty($payload->getPayOrderId())) {
                    throw new Exception('Missing pay order id in payload');
                }

                $action = $this->getAction();

                # Using TransactionStatusRequest for backwards compatibility, and refunds.
                $useLegacy = (stripos($action, 'refund') !== false || !$payload->isTguTransaction());
                $request = $useLegacy ? new TransactionStatusRequest($payload->getPayOrderId()) : new OrderStatusRequest($payload->getPayOrderId());

                $payOrder = $request->setConfig($config)->start();

                if (!$useLegacy && (in_array($action, ['new_ppt', 'cancel'])) && $payOrder->isCancelled()) {
                    // force retrieving status - paylink fix
                    paydbg('retrieve status through TransactionStatusRequest');
                    $request = new TransactionStatusRequest($payload->getPayOrderId());
                    $payOrder = $request->setConfig($config)->start();
                }

            } catch (PayException $e) {
                throw new Exception('API Retrieval error: ' . $payload->getPayOrderId() . ' - ' . $e->getFriendlyMessage());
            }
        }

        return $payOrder;
    }

    /**
     * @param $payload
     * @return int|mixed|null
     */
    private function getPayloadState($payload)
    {
        try {
            $payloadState = (new PayStatus())->get($payload->getInternalStateId());
        } catch (\Throwable $e) {
            $payloadState = null;
        }
        return $payloadState;
    }

    /**
     * @param string $username Token code
     * @param string $password API Token
     * @return boolean Returns true if the signing is successful and authorised
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
            paydbg('checkSignExchange: ' . $e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * @return boolean
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
    private function getRequestHeaders(): bool|array|string
    {
        if (empty($this->headers)) {
            $this->headers = array_change_key_case(getallheaders());
        }
        return $this->headers;
    }
}
