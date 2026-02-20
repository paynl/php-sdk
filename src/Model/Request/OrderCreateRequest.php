<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model\Request;

use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Model\Customer;
use PayNL\Sdk\Model\Order;
use PayNL\Sdk\Model\Stats;
use PayNL\Sdk\Model\Pay\PayOrder;
use PayNL\Sdk\Request\RequestData;
use PayNL\Sdk\Request\RequestInterface;
use PayNL\Sdk\Util\Vat;

/**
 * Class OrderCreateRequest
 *
 * @package PayNL\Sdk\Model\Request
 */
class OrderCreateRequest extends RequestData
{
    private string $serviceId;
    private string $description = '';
    private string $reference = '';
    private string $expire = '';
    private string $returnUrl;
    private string $exchangeUrl = '';
    private int $amount;
    private string $currency = 'EUR';
    private int $paymentMethodId;
    private int $issuerId;
    private string $paypalOrderId;
    private array $paymentInputData = [];
    private string $terminalCode;
    private ?bool $testMode = null;

    private ?Customer $customer = null;
    private ?Order $order = null;
    private ?Stats $stats = null;

    private string $notificationType = '';
    private string $notificationRecipient = '';
    private array $transferData = [];
    private array $optimize = [];

    public function __construct($mapperName = 'OrderCreate', $uri = 'orders', $method = RequestInterface::METHOD_POST)
    {
        parent::__construct($mapperName, $uri, $method);
    }

    /**
     * @param $requestForShippingAddress
     * @param $requestForBillingAddress
     * @param $requestForContactDetails
     * @return void
     */
    public function enableFastCheckout($requestForShippingAddress = true, $requestForBillingAddress = true, $requestForContactDetails = true): void
    {
        $this->optimize['flow'] = 'fastCheckout';
        $this->optimize['shippingAddress'] =  $requestForShippingAddress;
        $this->optimize['billingAddress'] =  $requestForBillingAddress;
        $this->optimize['contactDetails'] =  $requestForContactDetails;
    }

    /**
     * @param string $returnUrl
     * @return $this
     */
    public function setReturnurl(string $returnUrl): self
    {
        $this->returnUrl = $returnUrl;
        return $this;
    }

    /**
     * @return array
     */
    private function getProducts() : array
    {
        $products = [];

        foreach ($this->order->getProducts() as $objProduct) {
            $product = [];
            $product['id'] = $objProduct->getId();
            $product['description'] = $objProduct->getDescription();
            $product['type'] = $objProduct->getType();
            $product['price'] = [
                'value' => $objProduct->getPrice()->getValue(),
                'currency' => $objProduct->getPrice()->getCurrency(),
            ];
            $product['quantity'] = $objProduct->getQuantity();
            $product['vatPercentage'] = $objProduct->getVatPercentage();

            if (is_null($product['vatPercentage']) && !empty($objProduct->getVatCode())) {
                $product['vatPercentage'] = (new Vat())->getPercentageByClass($objProduct->getVatCode());
            }

            $products[] = $product;
        }

        return $products;
    }

    /**
     * @param $returnArr
     * @param $field
     * @param $value
     * @return void
     */
    private function add(&$returnArr, $field, $value)
    {
        if (!empty($value)) {
            $returnArr = array_merge($returnArr, [$field => $value]);
        }
    }

    /**
     * @param float $amount Whole amount. Not in cents.
     * @return $this
     */
    public function setAmount(float $amount): self
    {
        $this->amount = (int)round($amount * 100);
        return $this;
    }

    /**
     * @param string $currency
     * @return $this
     */
    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @param Order $order
     * @return $this
     */
    public function setOrder(Order $order): self
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @param Customer $customer
     * @return $this
     */
    public function setCustomer(Customer $customer): self
    {
        $this->customer = $customer;
        return $this;
    }

    /**
     * @param Stats $stats
     * @return $this
     */
    public function setStats(Stats $stats): self
    {
        $this->stats = $stats;
        return $this;
    }

    /**
     * @param string $serviceId
     * @return $this
     */
    public function setServiceId(string $serviceId): self
    {
        $this->serviceId = $serviceId;
        return $this;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @param string $reference
     * @return $this
     * @throws \Exception
     */
    public function setReference(string $reference): self
    {
        if (!ctype_alnum($reference)) {
            throw new \Exception('Reference should consist of all letters or digits');
        }
        $this->reference = $reference;
        return $this;
    }

    /**
     * @param string $exchangeUrl
     * @return $this
     */
    public function setExchangeUrl(string $exchangeUrl): self
    {
        $this->exchangeUrl = $exchangeUrl;
        return $this;
    }

    /**
     * @param int $paymentMethodId
     * @return $this
     */
    public function setPaymentMethodId(int $paymentMethodId): self
    {
        $this->paymentMethodId = $paymentMethodId;
        return $this;
    }

    /**
     * @param int $issuerId Bank id
     */
    public function setIssuerId(int $issuerId): self
    {
        $this->issuerId = $issuerId;
        return $this;
    }

    /**
     * Use when implementing express checkout for PayPal.
     *
     * @param string $orderId PayPal order ID
     * @return $this
     */
    public function setPayPalOrderId(string $orderId): self
    {
        $this->paypalOrderId = $orderId;
        return $this;
    }

    /**
     * Use this to provide the payment method with custom input data.
     *
     * @param array $inputData
     * @return $this
     */
    public function setPaymentInputData(array $inputData): self
    {
        $this->paymentInputData = $inputData;
        return $this;
    }

    /**
     * @param bool $testMode
     * @return $this
     */
    public function setTestmode(bool $testMode): self
    {
        $this->testMode = $testMode;
        return $this;
    }

    /**
     * @param string $type options: push or email
     * @param string $recipient options: ad-code or emailaddress, depending on type
     * @return $this
     * @throws \Exception
     */
    public function setNotification(string $type, string $recipient): self
    {
        $type = strtolower($type);
        if ($type == 'email') {
            if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception('Valid email format expected as notification recipient');
            }
        }
        if ($type == 'push') {
            if (!(substr(strtoupper($recipient),0,3) == 'AD-')) {
                throw new \Exception('Recepient expected to be AD-####-#### code');
            }
        }
        $this->notificationType = $type;
        $this->notificationRecipient = $recipient;
        return $this;
    }

    /**
     * @param array $transferData multidimensional array with one or more key and value
     */
    public function setTransferData(array $transferData): self
    {
        foreach ($transferData as $element) {
            foreach ($element as $key => $value) {
                $this->transferData[] = ['name' => $key, 'value' => $value];
            }
        }
        return $this;
    }

    /**
     * @return string[]
     */
    private function requiredArguments()
    {
        return ['amount', 'serviceId'];
    }

    /**
     * @return array
     */
    public function getPathParameters(): array
    {
        return [];
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getBodyParameters(): array
    {
        foreach ($this->requiredArguments() as $field) {
            if (empty($this->$field)) {
                throw new \Exception('Required param `' . $field . '` is empty');
            }
        }

        # Required parameters
        $parameters = [
            'serviceId' => $this->serviceId,
            'amount' => [
                'value' => $this->amount,
                'currency' => $this->currency,
            ],
        ];

        # Optional parameters
        $this->add($parameters, 'returnUrl', $this->returnUrl);
        $this->add($parameters, 'description', $this->description);
        $this->add($parameters, 'reference', $this->reference);
        $this->add($parameters, 'expire', $this->expire);
        $this->add($parameters, 'exchangeUrl', $this->exchangeUrl);

        if (!empty($this->paymentMethodId)) {
            $parameters['paymentMethod'] = ['id' => $this->paymentMethodId];

            $options = array_filter([
                'issuerId' => $this->issuerId ?? null,
                'terminalCode' => $this->terminalCode ?? null,
                'paypalOrderId' => $this->paypalOrderId ?? null,
                'paymentInputData' => !empty($this->paymentInputData) ? true : null,
            ]);

            if (count($options) > 1) {
                throw new \Exception('Only one of: issuerId, terminalCode, paypalOrderId or paymentInputData may be set.');
            }

            if (!empty($this->issuerId)) {
                $parameters['paymentMethod']['input']['issuerId'] = $this->issuerId;
            } elseif (!empty($this->terminalCode)) {
                $parameters['paymentMethod']['input']['terminalCode'] = $this->terminalCode;
            } elseif (!empty($this->paypalOrderId)) {
                $parameters['paymentMethod']['input']['orderId'] = $this->paypalOrderId;
            } elseif (!empty($this->paymentInputData)) {
                $parameters['paymentMethod']['input'] = $this->paymentInputData;
            }
        }

        $parameters['integration']['test'] = $this->testMode === true;

        if (!empty($this->optimize)) {
            $this->add($parameters, 'optimize', $this->optimize);
        }

        if ($this->customer instanceof Customer) {
            $custParameters = [];
            $this->add($custParameters, 'firstName', $this->customer->getFirstName());
            $this->add($custParameters, 'lastName', $this->customer->getLastName());
            $this->add($custParameters, 'ipAddress', $this->customer->getIpAddress());
            $this->add($custParameters, 'birthDate', $this->customer->getBirthDate());
            $this->add($custParameters, 'gender', $this->customer->getGender());
            $this->add($custParameters, 'phone', $this->customer->getPhone());
            $this->add($custParameters, 'email', $this->customer->getEmail());
            $this->add($custParameters, 'language', $this->customer->getLanguage());
            $this->add($custParameters, 'trust', $this->customer->getTrust());
            $this->add($custParameters, 'reference', $this->customer->getReference());
            $this->add($custParameters, 'locale', $this->customer->getLocale());

            $compParameters = [];
            $this->add($compParameters, 'name', $this->customer->getCompany()->getName());
            $this->add($compParameters, 'cocNumber', $this->customer->getCompany()->getCoc());
            $this->add($compParameters, 'vatNumber', $this->customer->getCompany()->getVat());
            $this->add($compParameters, 'countryCode', $this->customer->getCompany()->getCountryCode());

            $this->add($custParameters, 'company', $compParameters);
            $this->add($parameters, 'customer', $custParameters);
        }

        if ($this->order instanceof Order) {
            $orderParameters = [];
            $this->add($orderParameters, 'countryCode', $this->order->getCountryCode());
            $this->add($orderParameters, 'deliveryDate', $this->order->getDeliveryDate());
            $this->add($orderParameters, 'invoiceDate', $this->order->getInvoiceDate());

            $deliveryAddress = [];
            $this->add($deliveryAddress, 'code', $this->order->getDeliveryAddress()->getCode());
            $this->add($deliveryAddress, 'street', $this->order->getDeliveryAddress()->getStreetName());
            $this->add($deliveryAddress, 'streetNumber', $this->order->getDeliveryAddress()->getStreetNumber());
            $this->add($deliveryAddress, 'streetNumberExtension', $this->order->getDeliveryAddress()->getStreetNumberExtension());
            $this->add($deliveryAddress, 'zipCode', $this->order->getDeliveryAddress()->getZipCode());
            $this->add($deliveryAddress, 'city', $this->order->getDeliveryAddress()->getCity());
            $this->add($deliveryAddress, 'region', $this->order->getDeliveryAddress()->getRegionCode());
            $this->add($deliveryAddress, 'country', $this->order->getDeliveryAddress()->getCountryCode());
            $this->add($orderParameters, 'deliveryAddress', $deliveryAddress);

            $invoiceAddress = [];
            $this->add($invoiceAddress, 'code', $this->order->getInvoiceAddress()->getCode());
            $this->add($invoiceAddress, 'street', $this->order->getInvoiceAddress()->getStreetName());
            $this->add($invoiceAddress, 'streetNumber', $this->order->getInvoiceAddress()->getStreetNumber());
            $this->add($invoiceAddress, 'streetNumberExtension', $this->order->getInvoiceAddress()->getStreetNumberExtension());
            $this->add($invoiceAddress, 'zipCode', $this->order->getInvoiceAddress()->getZipCode());
            $this->add($invoiceAddress, 'city', $this->order->getInvoiceAddress()->getCity());
            $this->add($invoiceAddress, 'region', $this->order->getInvoiceAddress()->getRegionCode());
            $this->add($invoiceAddress, 'country', $this->order->getInvoiceAddress()->getCountryCode());
            $this->add($orderParameters, 'invoiceAddress', $invoiceAddress);
            $this->add($orderParameters, 'products', $this->getProducts());
            $this->add($parameters, 'order', $orderParameters);
        }

        if ($this->stats instanceof Stats) {
            $stats = [];
            $this->add($stats, 'info', $this->stats->getInfo());
            $this->add($stats, 'tool', $this->stats->getTool());
            $this->add($stats, 'object', $this->getSdkObject($this->stats));
            $this->add($stats, 'extra1', $this->stats->getExtra1());
            $this->add($stats, 'extra2', $this->stats->getExtra2());
            $this->add($stats, 'extra3', $this->stats->getExtra3());
            $this->add($stats, 'domainId', $this->stats->getDomainId());
            $this->add($parameters, 'stats', $stats);
        }

        if (!empty($this->notificationType)) {
            $parameters['notification'] = [
                'type' => $this->notificationType,
                'recipient' => $this->notificationRecipient
            ];
        }

        $this->add($parameters, 'transferData', $this->transferData);

        return $parameters;
    }

    /**
     * @param Stats $stats
     * @return string
     */
    private function getSdkObject(Stats $stats)
    {
        $__object = $this->stats->getObject();

        if (empty($__object)) {
            $composerFilePath = sprintf('%s/%s', rtrim(__DIR__, '/'), '../../../composer.json');

            if (file_exists($composerFilePath)) {
                $composer = json_decode(file_get_contents($composerFilePath), true);

                if (isset($composer['version'])) {
                    $composerVersion = $composer['version'];
                }
            }

            $__object = 'PHP-SDK ' . ($composerVersion ?? 'unknown');
        }
        return $__object;
    }

    /**
     * @param string $expire
     */
    public function setExpire(string $expire): void
    {
        $this->expire = $expire;
    }

    /**
     * @param string $terminalCode TH-code, use to set the terminal.
     */
    public function setTerminal(string $terminalCode): void
    {
        $this->terminalCode = $terminalCode;
    }

    /**
     * @return PayOrder
     * @throws PayException
     */
    public function start($version = 1): PayOrder
    {
        $this->config->setversion($version);
        return parent::start();
    }
}