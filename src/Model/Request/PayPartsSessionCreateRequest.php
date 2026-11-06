<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model\Request;

use PayNL\Sdk\Model\Amount;
use PayNL\Sdk\Model\Customer;
use PayNL\Sdk\Model\Order;
use PayNL\Sdk\Model\Response\PayPartsSessionCreateResponse;
use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Request\RequestInterface;
use PayNL\Sdk\Request\RequestData;

/**
 * Class PayPartsSessionCreateRequest
 *
 * Create a session for the embedded PayParts checkout.
 */
class PayPartsSessionCreateRequest extends RequestData
{
    public const API_URL = 'https://parts.pay.nl';
    public const ZERO_API_URL = 'https://zero-parts.pay.nl';

    private bool $zero = false;

    private OrderCreateRequest $orderRequest;
    private ?Order $order = null;
    private array $integration = [];

    /**
     * Initialize the session endpoint and reuse the SDK order payload builder.
     */
    public function __construct()
    {
        parent::__construct('PayPartsSessionCreate', 'session/start', RequestInterface::METHOD_POST);
        $this->orderRequest = new OrderCreateRequest();
    }

    /**
     * Use the Zero pre-production environment, or pass false to use production.
     * @param boolean $useZero
     * @return $this
     */
    public function useZero(bool $useZero = true): self
    {
        $this->zero = $useZero;
        return $this;
    }

    /**
     * Amount in major currency units, or an Amount containing cents.
     * @param float|Amount $amount
     * @return $this
     */
    public function setAmount(float|Amount $amount): self
    {
        $this->orderRequest->setAmount($amount);
        return $this;
    }

    /**
     * Set the currency.
     * @param string $currency
     * @return $this
     */
    public function setCurrency(string $currency): self
    {
        $this->orderRequest->setCurrency($currency);
        return $this;
    }

    /**
     * Set the return URL.
     * @param string $returnUrl
     * @return $this
     */
    public function setReturnUrl(string $returnUrl): self
    {
        $this->orderRequest->setReturnUrl($returnUrl);
        return $this;
    }

    /**
     * Set the webhook URL.
     * @param string $exchangeUrl
     * @return $this
     */
    public function setExchangeUrl(string $exchangeUrl): self
    {
        $this->orderRequest->setExchangeUrl($exchangeUrl);
        return $this;
    }

    /**
     * Set the payment description.
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description): self
    {
        $this->orderRequest->setDescription($description);
        return $this;
    }

    /**
     * Set the merchant reference.
     * @param string $reference
     * @return $this
     * @throws \Exception
     */
    public function setReference(string $reference): self
    {
        $this->orderRequest->setReference($reference);
        return $this;
    }

    /**
     * Set the expiry as an ISO-8601 timestamp.
     * @param string $expire
     * @return $this
     */
    public function setExpire(string $expire): self
    {
        $this->orderRequest->setExpire($expire);
        return $this;
    }

    /**
     * Set the customer details.
     * @param Customer $customer
     * @return $this
     */
    public function setCustomer(Customer $customer): self
    {
        $this->orderRequest->setCustomer($customer);
        return $this;
    }

    /**
     * Set addresses and order lines.
     * @param Order $order
     * @return $this
     */
    public function setOrder(Order $order): self
    {
        $this->orderRequest->setOrder($order);
        $this->order = $order;
        return $this;
    }

    /**
     * Set optional integration settings, such as checkoutUrl.
     * @param array $integration
     * @return $this
     */
    public function setIntegration(array $integration): self
    {
        $this->integration = $integration;
        return $this;
    }

    /**
     * No path parameters are needed for session creation.
     * @return array
     */
    public function getPathParameters(): array
    {
        return [];
    }

    /**
     * Build the session payload using the same monetary units as OrderCreateRequest.
     * @return array
     * @throws PayException
     * @throws \Exception
     */
    public function getBodyParameters(): array
    {
        $parameters = $this->orderRequest->getBodyParameters();

        unset($parameters['serviceId'], $parameters['integration']);
        foreach (['returnUrl', 'exchangeUrl'] as $field) {
            if (empty($parameters[$field])) {
                throw new PayException('Required param `' . $field . '` is empty', 0, 0);
            }
        }
        if ($parameters['amount']['value'] <= 0) {
            throw new PayException('Amount must be greater than zero', 0, 0);
        }
        if ($this->order !== null) {
            foreach (['deliveryAddress', 'invoiceAddress'] as $field) {
                $address = $field === 'deliveryAddress' ? $this->order->getDeliveryAddress() : $this->order->getInvoiceAddress();
                foreach (['firstName', 'lastName'] as $name) {
                    $value = $address->{'get' . ucfirst($name)}();
                    if ($value !== '') {
                        $parameters['order'][$field][$name] = $value;
                    }
                }
            }
        }
        if ($this->integration !== []) {
            $parameters['integration'] = $this->integration;
        }
        return $parameters;
    }

    /**
     * Create the session and return its ID and token.
     * @return PayPartsSessionCreateResponse
     * @throws PayException
     */
    public function start(): PayPartsSessionCreateResponse
    {
        $this->config = clone $this->config;

        $this->config->setCore($this->zero ? self::ZERO_API_URL : self::API_URL);
        $this->config->setVersion(1);
        $response = parent::start();
        if ($response->getSessionId() === '' || $response->getSessionToken() === '') {
            throw new PayException('Could not start PayParts session: missing session ID or token', 0, 0);
        }
        return $response;
    }
}
