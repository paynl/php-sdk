<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model\Request;

use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Model\Amount;
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

    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct('OrderCreate', 'orders', RequestInterface::METHOD_POST);
    }

    /**
     * @param boolean $requestForShippingAddress
     * @param boolean $requestForBillingAddress
     * @param boolean $requestForContactDetails
     * @return void
     */
    public function enableFastCheckout($requestForShippingAddress = true, $requestForBillingAddress = true, $requestForContactDetails = true): void
    {
        $this->optimize['flow'] = 'fastCheckout';
        $this->optimize['shippingAddress'] = $requestForShippingAddress;
        $this->optimize['billingAddress'] = $requestForBillingAddress;
        $this->optimize['contactDetails'] = $requestForContactDetails;
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
    private function getProducts(): array
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
     * @param array  $returnArr
     * @param string $field
     * @param mixed  $value
     * @return void
     */
    private function addField(&$returnArr, $field, $value)
    {
        if (!empty($value)) {
            $returnArr = array_merge($returnArr, [$field => $value]);
        }
    }

    /**
     * @param float|Amount $amount Whole amount. Not in cents. Or Amount object.
     * @return $this
     */
    public function setAmount(float|Amount $amount): self
    {
        if ($amount instanceof Amount) {
            $this->amount = $amount->getValue();
            $this->currency = $amount->getCurrency();
            return $this;
        }
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
        if (!preg_match('/^([A-Za-z0-9-]+)$/', $reference)) {
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
     * @param integer $paymentMethodId
     * @return $this
     */
    public function setPaymentMethodId(int $paymentMethodId): self
    {
        $this->paymentMethodId = $paymentMethodId;
        return $this;
    }

    /**
     * @param integer $issuerId Bank id.
     */
    public function setIssuerId(int $issuerId): self
    {
        $this->issuerId = $issuerId;
        return $this;
    }

    /**
     * Use when implementing express checkout for PayPal.
     *
     * @param string $orderId PayPal order ID.
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
     * @param boolean $testMode
     * @return $this
     */
    public function setTestmode(bool $testMode): self
    {
        $this->testMode = $testMode;
        return $this;
    }

    /**
     * @param string $type      Options: push or email.
     * @param string $recipient Options: ad-code or emailaddress, depending on type.
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
            if (!(substr(strtoupper($recipient), 0, 3) == 'AD-')) {
                throw new \Exception('Recepient expected to be AD-####-#### code');
            }
        }
        $this->notificationType = $type;
        $this->notificationRecipient = $recipient;
        return $this;
    }

    /**
     * @param array $transferData Multidimensional array with one or more key and value.
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
        $this->addField($parameters, 'returnUrl', $this->returnUrl);
        $this->addField($parameters, 'description', $this->description);
        $this->addField($parameters, 'reference', $this->reference);
        $this->addField($parameters, 'expire', $this->expire);
        $this->addField($parameters, 'exchangeUrl', $this->exchangeUrl);

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
            $this->addField($parameters, 'optimize', $this->optimize);
        }

        if ($this->customer instanceof Customer) {
            $custParameters = [];
            $this->addField($custParameters, 'firstName', $this->customer->getFirstName());
            $this->addField($custParameters, 'lastName', $this->customer->getLastName());
            $this->addField($custParameters, 'ipAddress', $this->customer->getIpAddress());
            $this->addField($custParameters, 'birthDate', $this->customer->getBirthDate());
            $this->addField($custParameters, 'gender', $this->customer->getGender());
            $this->addField($custParameters, 'phone', $this->customer->getPhone());
            $this->addField($custParameters, 'email', $this->customer->getEmail());
            $this->addField($custParameters, 'language', $this->customer->getLanguage());
            $this->addField($custParameters, 'trust', $this->customer->getTrust());
            $this->addField($custParameters, 'reference', $this->customer->getReference());
            $this->addField($custParameters, 'locale', $this->customer->getLocale());

            $compParameters = [];
            $this->addField($compParameters, 'name', $this->customer->getCompany()->getName());
            $this->addField($compParameters, 'cocNumber', $this->customer->getCompany()->getCoc());
            $this->addField($compParameters, 'vatNumber', $this->customer->getCompany()->getVat());
            $this->addField($compParameters, 'countryCode', $this->customer->getCompany()->getCountryCode());

            $this->addField($custParameters, 'company', $compParameters);
            $this->addField($parameters, 'customer', $custParameters);
        }

        if ($this->order instanceof Order) {
            $orderParameters = [];
            $this->addField($orderParameters, 'countryCode', $this->order->getCountryCode());
            $this->addField($orderParameters, 'deliveryDate', $this->order->getDeliveryDate());
            $this->addField($orderParameters, 'invoiceDate', $this->order->getInvoiceDate());

            $deliveryAddress = [];
            $this->addField($deliveryAddress, 'code', $this->order->getDeliveryAddress()->getCode());
            $this->addField($deliveryAddress, 'street', $this->order->getDeliveryAddress()->getStreetName());
            $this->addField($deliveryAddress, 'streetNumber', $this->order->getDeliveryAddress()->getStreetNumber());
            $this->addField($deliveryAddress, 'streetNumberExtension', $this->order->getDeliveryAddress()->getStreetNumberExtension());
            $this->addField($deliveryAddress, 'zipCode', $this->order->getDeliveryAddress()->getZipCode());
            $this->addField($deliveryAddress, 'city', $this->order->getDeliveryAddress()->getCity());
            $this->addField($deliveryAddress, 'region', $this->order->getDeliveryAddress()->getRegionCode());
            $this->addField($deliveryAddress, 'country', $this->order->getDeliveryAddress()->getCountryCode());
            $this->addField($orderParameters, 'deliveryAddress', $deliveryAddress);

            $invoiceAddress = [];
            $this->addField($invoiceAddress, 'code', $this->order->getInvoiceAddress()->getCode());
            $this->addField($invoiceAddress, 'street', $this->order->getInvoiceAddress()->getStreetName());
            $this->addField($invoiceAddress, 'streetNumber', $this->order->getInvoiceAddress()->getStreetNumber());
            $this->addField($invoiceAddress, 'streetNumberExtension', $this->order->getInvoiceAddress()->getStreetNumberExtension());
            $this->addField($invoiceAddress, 'zipCode', $this->order->getInvoiceAddress()->getZipCode());
            $this->addField($invoiceAddress, 'city', $this->order->getInvoiceAddress()->getCity());
            $this->addField($invoiceAddress, 'region', $this->order->getInvoiceAddress()->getRegionCode());
            $this->addField($invoiceAddress, 'country', $this->order->getInvoiceAddress()->getCountryCode());
            $this->addField($orderParameters, 'invoiceAddress', $invoiceAddress);
            $this->addField($orderParameters, 'products', $this->getProducts());
            $this->addField($parameters, 'order', $orderParameters);
        }

        if ($this->stats instanceof Stats) {
            $stats = [];
            $this->addField($stats, 'info', $this->stats->getInfo());
            $this->addField($stats, 'tool', $this->stats->getTool());
            $this->addField($stats, 'object', $this->getSdkObject($this->stats));
            $this->addField($stats, 'extra1', $this->stats->getExtra1());
            $this->addField($stats, 'extra2', $this->stats->getExtra2());
            $this->addField($stats, 'extra3', $this->stats->getExtra3());
            $this->addField($stats, 'domainId', $this->stats->getDomainId());
            $this->addField($parameters, 'stats', $stats);
        }

        if (!empty($this->notificationType)) {
            $parameters['notification'] = [
                'type' => $this->notificationType,
                'recipient' => $this->notificationRecipient
            ];
        }

        $this->addField($parameters, 'transferData', $this->transferData);

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
    public function start(): PayOrder
    {
        $this->config->setversion(1);
        return parent::start();
    }
}
