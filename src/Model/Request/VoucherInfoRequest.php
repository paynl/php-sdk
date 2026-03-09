<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model\Request;

use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Request\RequestData;
use PayNL\Sdk\Request\RequestInterface;
use PayNL\Sdk\Model\Response\VoucherInfoResponse;

/**
 * Class VoucherInfoRequest
 *
 * @package PayNL\Sdk\Model\Request
 */
class VoucherInfoRequest extends RequestData
{
    private string $serviceId;
    private string $pointOfInteraction = '';
    private array $pointOfInteraction_types = ['ON_THE_MOVE', 'ECOMMERCE', 'IN_PERSON', 'INVOICE', 'DEBT_COLLECTION', 'FUNDING', 'PAYMENT_REQUEST', 'RECURRING', 'UNATTENDED', 'MOTO', 'PAYOUT'];
    private string $cardNumber = '';
    private string $pinCode = '';

    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct('VoucherInfo', 'vouchers/info', RequestInterface::METHOD_POST);
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
     * @return string[]
     */
    private function requiredArguments()
    {
        return ['serviceId', 'cardNumber'];
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

        $this->addField($parameters, 'serviceId', $this->serviceId);

        $integrationParameters = [];
        $this->addField($integrationParameters, 'pointOfInteraction', $this->pointOfInteraction);
        $this->addField($parameters, 'integration', $integrationParameters);

        $voucherParameters = [];
        $this->addField($voucherParameters, 'number', $this->cardNumber);
        $this->addField($voucherParameters, 'pincode', $this->pinCode);
        $this->addField($parameters, 'voucher', $voucherParameters);

        return $parameters;
    }

    /**
     * @return VoucherInfoResponse
     * @throws PayException
     */
    public function start(): VoucherInfoResponse
    {
        $this->config->setVersion(2);
        return parent::start();
    }
}
