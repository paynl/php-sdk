<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model\Request;

use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Request\RequestInterface;
use PayNL\Sdk\Model\Pay\PayOrder;

/**
 * Class VoucherPaymentRequest
 *
 * @package PayNL\Sdk\Model\Request
 */
class VoucherPaymentRequest extends OrderCreateRequest
{
    private string $pointOfInteraction = '';
    private array $pointOfInteraction_types = ['ON_THE_MOVE', 'ECOMMERCE', 'IN_PERSON', 'INVOICE', 'DEBT_COLLECTION', 'FUNDING', 'PAYMENT_REQUEST', 'RECURRING', 'UNATTENDED', 'MOTO', 'PAYOUT'];
    private string $cardNumber = '';
    private string $pinCode = '';


    public function __construct()
    {
        parent::__construct('VoucherPayment', 'vouchers/payment', RequestInterface::METHOD_POST);
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
        return ['cardNumber', 'pointOfInteraction'];
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

        if (empty($this->pinCode) && $this->pointOfInteraction != 'IN_PERSON') {
            throw new \Exception('Required param `pinCode` is empty');
        }

        $orderParameters = parent::getBodyParameters();

        $voucherParameters = [
                'voucher' => [
                    'number' => $this->cardNumber,
                ],
                'pointOfInteraction' => $this->pointOfInteraction,
        ];        

        if ($this->pointOfInteraction != 'IN_PERSON') {
            $voucherParameters['voucher']['pincode'] = $this->pinCode;
        }

        $parameters = array_merge($orderParameters, $voucherParameters);

        return $parameters;
    }

    /**
     * @return PayOrder
     * @throws PayException
     */
    public function start($version = 2): PayOrder
    {
        $this->config->setVersion($version);
        return parent::start($version);
    }
}