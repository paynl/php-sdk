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
    public function __construct(array $payload = null)
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
     * @return string
     * @throws PayException
     */
    public function getAction()
    {
        try {
            $payload = $this->getPayload();
        } catch (\Throwable $e) {
            throw new PayException('Could not retrieve action: ' . $e->getMessage(), 0, 0);
        }
        return $payload->getAction();
    }

    /**
     * @return string
     * @throws PayException
     */
    public function getReference()
    {
        try {
            $payload = $this->getPayload();
        } catch (\Throwable $e) {
            throw new PayException('Could not retrieve reference: ' . $e->getMessage(), 0, 0);
        }
        return $payload->getReference();
    }

    /**
     * @return string
     * @throws PayException
     */
    public function getPayOrderId()
    {
        try {
            $payload = $this->getPayload();
        } catch (\Throwable $e) {
            throw new PayException('Could not retrieve payOrderId: ' . $e->getMessage(), 0, 0);
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
            $reference = $request[$this->getGmsReferenceKey()] ?? null;
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
     * @param Config |null $config
     * @return PayOrder
     * @throws Exception
     */
    public function process(Config $config = null): PayOrder
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
                $payOrder->setType($payload->getType());
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
            dbg('checkSignExchange: ' . $e->getMessage());
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
