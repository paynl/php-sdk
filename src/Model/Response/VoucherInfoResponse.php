<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model\Response;

use PayNL\Sdk\Model\ModelInterface;
use PayNL\Sdk\Model\Amount;

/**
 * Class VoucherPaymentResponse
 *
 * @package PayNL\Sdk\Model
 */
class VoucherInfoResponse implements ModelInterface
{
    /**
     * @var array
     */
    protected $integration; 

    /**
    * @var array
     */
    protected $voucher;   

    /**
     * @param array $integration
     * @return $this
     */
    public function setIntegration(array $integration): self
    {
        $this->integration = $integration;       
        return $this;
    }    

    /**
     * @param array $voucher
     * @return $this
     */
    public function setVoucher(array $voucher): self
    { 
        $this->voucher = $voucher;
        return $this;
    }

    /**
     * @return array
     */
    public function getIntegration(): array
    {
        return $this->integration;
    }   

    /**
     * @return boolean
     */
    public function getTest(): bool
    {
        return $this->integration['test'] ?? false;
    }

    /**
     * @return string
     */
    public function getPointOfInteraction(): string
    {
        return $this->integration['pointOfInteraction'] ?? '';
    }    

    /**
     * @return array
     */
    public function getVoucher(): array
    {
        return $this->voucher;
    }    

    /**
     * @return float|integer
     */
    public function getAmount(): float|int
    {
        return $this->voucher['amount']['value'] / 100 ?? 0;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return (string)$this->voucher['amount']['currency'] ?? '';
    }  

    /**
     * @return string
     */
    public function getExpiresAt(): string
    {
        return (string) $this->voucher['expiresAt'] ?? '';
    }   

    /**
     * @return array
     */
    public function getBrand(): array
    {
        return $this->voucher['brand'] ?? [];
    }

    /**
     * @return integer
     */
    public function getBrandId(): int
    {
        return $this->voucher['brand']['id'] ?? '';
    }

    /**
     * @return string
     */
    public function getBrandName(): string
    {
        return $this->voucher['brand']['name'] ?? '';
    }

    /**
     * @return string
     */
    public function getBrandIconUrl(): string
    {
        return $this->voucher['brand']['iconUrl'] ?? '';
    }
    
}
