<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model\Request;

use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Model\Customer;
use PayNL\Sdk\Model\Order;
use PayNL\Sdk\Model\Stats;
use PayNL\Sdk\Request\RequestData;
use PayNL\Sdk\Request\RequestInterface;
use PayNL\Sdk\Util\Vat;
use PayNL\Sdk\Model\Response\VoucherPaymentResponse;

/**
 * Class VoucherPaymentRequest
 *
 * @package PayNL\Sdk\Model\Request
 */
class VoucherPaymentRequest extends RequestData
{
    private string $serviceId;
    private string $pointOfInteraction = '';
    private array $pointOfInteraction_types = ['ON_THE_MOVE', 'ECOMMERCE', 'IN_PERSON', 'INVOICE', 'DEBT_COLLECTION', 'FUNDING', 'PAYMENT_REQUEST', 'RECURRING', 'UNATTENDED', 'MOTO', 'PAYOUT'];
    private string $cardNumber = '';
    private string $pinCode = '';
    private string $reference = '';
    private string $description = '';
    private string $returnUrl = '';
    private string $exchangeUrl = '';
    private int $amount;
    private string $currency = 'EUR';
    private ?Customer $customer = null;
    private ?Order $order = null;
    private ?Stats $stats = null;
    private array $transferData = [];

    /**
     * VoucherPaymentRequest constructor.
     */
    public function __construct()
    {
        parent::__construct('VoucherPayment', 'vouchers/payment', RequestInterface::METHOD_POST);
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
     * @param array $returnArr
     * @param string $field
     * @param mixed $value
     * @return void
     */
    private function _add(&$returnArr, $field, $value)
    {
        if (!empty($value)) {
            $returnArr = array_merge($returnArr, [$field => $value]);
        }
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
     * @param string $cardNumber
     * @return $this
     */
    public function setCardNumber(string $cardNumber): self
    {
        $this->cardNumber = $cardNumber;
        return $this;
    }

    /**
     * @param string $pinCode
     * @return $this
     */
    public function setPinCode(string $pinCode): self
    {
        $this->pinCode = $pinCode;
        return $this;
    }

    /**
     * @param string $pointOfInteraction
     * @return $this
     */
    public function setPointOfInteraction(string $pointOfInteraction): self
    {
        if (!in_array($pointOfInteraction, $this->pointOfInteraction_types, true)) {
            throw new \Exception('pointOfInteraction should be one of: ' . implode(', ', $this->pointOfInteraction_types));
        }
        $this->pointOfInteraction = $pointOfInteraction;
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
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;
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
     * @param string $returnUrl
     * @return $this
     */
    public function setReturnUrl(string $returnUrl): self
    {
        $this->returnUrl = $returnUrl;
        return $this;
    }

    /**
     * @param float $amount In cents.
     * @return $this
     */
    public function setAmount(float $amount): self
    {
        $this->amount = (int) round($amount * 100);
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
        return ['serviceId', 'cardNumber', 'pinCode', 'amount', 'currency'];
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

        $parameters = [];

        $this->_add($parameters, 'serviceId', $this->serviceId);
        $this->_add($parameters, 'description', $this->description);
        $this->_add($parameters, 'reference', $this->reference);
        $this->_add($parameters, 'returnUrl', $this->returnUrl);
        $this->_add($parameters, 'exchangeUrl', $this->exchangeUrl);
        $this->_add($parameters, 'pointOfInteraction', $this->pointOfInteraction);

        $amountParameters = [];
        $this->_add($amountParameters, 'value', $this->amount);
        $this->_add($amountParameters, 'currency', $this->currency);
        $this->_add($parameters, 'amount', $amountParameters);

        $voucherParameters = [];
        $this->_add($voucherParameters, 'number', $this->cardNumber);
        $this->_add($voucherParameters, 'pincode', $this->pinCode);
        $this->_add($parameters, 'voucher', $voucherParameters);

        if ($this->order instanceof Order) {
            $orderParameters = [];
            $this->_add($orderParameters, 'reference', $this->reference);
            $this->_add($orderParameters, 'description', $this->description);
            $this->_add($orderParameters, 'exchangeUrl', $this->exchangeUrl);
            $this->_add($orderParameters, 'deliveryDate', $this->order->getDeliveryDate());
            $this->_add($orderParameters, 'invoiceDate', $this->order->getInvoiceDate());

            $amountParameters = [];
            $this->_add($amountParameters, 'value', $this->amount);
            $this->_add($amountParameters, 'currency', $this->currency);
            $this->_add($orderParameters, 'amount', $amountParameters);

            $deliveryAddress = [];
            $this->_add($deliveryAddress, 'street', $this->order->getDeliveryAddress()->getStreetName());
            $this->_add($deliveryAddress, 'streetNumber', $this->order->getDeliveryAddress()->getStreetNumber());
            $this->_add($deliveryAddress, 'streetNumberExtension', $this->order->getDeliveryAddress()->getStreetNumberExtension());
            $this->_add($deliveryAddress, 'city', $this->order->getDeliveryAddress()->getCity());
            $this->_add($deliveryAddress, 'zipCode', $this->order->getDeliveryAddress()->getZipCode());
            $this->_add($deliveryAddress, 'country', $this->order->getDeliveryAddress()->getCountryCode());
            $this->_add($deliveryAddress, 'region', $this->order->getDeliveryAddress()->getRegionCode());
            $this->_add($orderParameters, 'deliveryAddress', $deliveryAddress);

            $invoiceAddress = [];
            $this->_add($invoiceAddress, 'street', $this->order->getInvoiceAddress()->getStreetName());
            $this->_add($invoiceAddress, 'streetNumber', $this->order->getInvoiceAddress()->getStreetNumber());
            $this->_add($invoiceAddress, 'streetNumberExtension', $this->order->getInvoiceAddress()->getStreetNumberExtension());
            $this->_add($invoiceAddress, 'city', $this->order->getInvoiceAddress()->getCity());
            $this->_add($invoiceAddress, 'zipCode', $this->order->getInvoiceAddress()->getZipCode());
            $this->_add($invoiceAddress, 'country', $this->order->getInvoiceAddress()->getCountryCode());
            $this->_add($invoiceAddress, 'region', $this->order->getInvoiceAddress()->getRegionCode());
            $this->_add($orderParameters, 'invoiceAddress', $invoiceAddress);

            if ($this->customer instanceof Customer) {
                $custParameters = [];
                $this->_add($custParameters, 'firstName', $this->customer->getFirstName());
                $this->_add($custParameters, 'lastName', $this->customer->getLastName());
                $this->_add($custParameters, 'ipAddress', $this->customer->getIpAddress());
                $this->_add($custParameters, 'birthDate', $this->customer->getBirthDate());
                $this->_add($custParameters, 'gender', $this->customer->getGender());
                $this->_add($custParameters, 'phone', $this->customer->getPhone());
                $this->_add($custParameters, 'email', $this->customer->getEmail());
                $this->_add($custParameters, 'language', $this->customer->getLanguage());
                $this->_add($custParameters, 'trust', $this->customer->getTrust());
                $this->_add($custParameters, 'reference', $this->customer->getReference());
                $this->_add($custParameters, 'locale', $this->customer->getLocale());

                $compParameters = [];
                $this->_add($compParameters, 'name', $this->customer->getCompany()->getName());
                $this->_add($compParameters, 'cocNumber', $this->customer->getCompany()->getCoc());
                $this->_add($compParameters, 'vatNumber', $this->customer->getCompany()->getVat());
                $this->_add($compParameters, 'countryCode', $this->customer->getCompany()->getCountryCode());
                $this->_add($custParameters, 'company', $compParameters);

                $this->_add($orderParameters, 'customer', $custParameters);
            }

            $this->_add($orderParameters, 'products', $this->getProducts());
            $this->_add($parameters, 'order', $orderParameters);
        }

        if ($this->stats instanceof Stats) {
            $stats = [];
            $this->_add($stats, 'info', $this->stats->getInfo());
            $this->_add($stats, 'tool', $this->stats->getTool());
            $this->_add($stats, 'object', $this->getSdkObject($this->stats));
            $this->_add($stats, 'extra1', $this->stats->getExtra1());
            $this->_add($stats, 'extra2', $this->stats->getExtra2());
            $this->_add($stats, 'extra3', $this->stats->getExtra3());
            $this->_add($stats, 'domainId', $this->stats->getDomainId());
            $this->_add($parameters, 'stats', $stats);
        }

        $this->_add($parameters, 'transferData', $this->transferData);

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
     * @return VoucherPaymentResponse
     * @throws PayException
     */
    public function start(): VoucherPaymentResponse
    {
        $this->config->setVersion(2);
        return parent::start();
    }
}
