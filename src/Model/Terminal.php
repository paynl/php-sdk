<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model;

/**
 * Class Terminal
 *
 * @package PayNL\Sdk\Model
 */
class Terminal implements ModelInterface
{
    /**
     * @var string
     */
    protected $code;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $attribution;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var string
     */
    protected $connectionStatus;

    /**
     * @var array
     */
    protected $merchant;

    /**
     * @var array
     */
    protected $service;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $terminalType;

    /**
     * @var string
     */
    protected $ecrProtocol;

    /**
     * @var string
     */
    protected $contractStartDate;

    /**
     * @var string
     */
    protected $contractEndDate;

    /**
     * @var array
     */
    protected $paymentTypes;

    /**
     * @var array
     */
    protected $terminalBrands;

    /**
     * @var array
     */
    protected $location;

    /**
     * @var string
     */
    protected $createdAt;

    /**
     * @var string
     */
    protected $createdBy;

    /**
     * @var string
     */
    protected $modifiedAt;

    /**
     * @var string
     */
    protected $modifiedBy;

    /**
     * @var string
     */
    protected $deletedAt;

    /**
     * @var string
     */
    protected $deletedBy;

    /**
     * @var string
     */
    protected $_links;


    /**
     * @return string
     */
    public function getCode(): string
    {
        return (string)$this->code;
    }

    /**
     * @param string $code
     * @return $this
     */
    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return (string)$this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getAttribution(): string
    {
        return (string)$this->attribution;
    }

    /**
     * @param string $attribution
     * @return $this
     */
    public function setAttribution(string $attribution): self
    {
        $this->attribution = $attribution;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return (string)$this->status;
    }

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return string
     */
    public function getConnectionStatus(): string
    {
        return (string)$this->connectionStatus;
    }

    /**
     * @param string $connectionStatus
     * @return $this
     */
    public function setConnectionStatus(string $connectionStatus): self
    {
        $this->connectionStatus = $connectionStatus;
        return $this;
    }

    /**
     * @return array
     */
    public function getMerchant(): array
    {
        return $this->merchant;
    }

    /**
     * @param array $merchant
     * @return $this
     */
    public function setMerchant(array $merchant): self
    {
        $this->merchant = $merchant;
        return $this;
    }

    /**
     * @return array
     */
    public function getService(): array
    {
        return $this->service;
    }

    /**
     * @param array $service
     * @return $this
     */
    public function setService(array $service): self
    {
        $this->service = $service;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return (string)$this->description;
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
     * @return string
     */
    public function getTerminalType(): string
    {
        return (string)$this->terminalType;
    }

    /**
     * @param string $terminalType
     * @return $this
     */
    public function setTerminalType(string $terminalType): self
    {
        $this->terminalType = $terminalType;
        return $this;
    }

    /**
     * @return string
     */
    public function getEcrProtocol(): string
    {
        return (string)$this->ecrProtocol;
    }

    /**
     * @param string $ecrProtocol
     * @return $this
     */
    public function setEcrProtocol(string $ecrProtocol): self
    {
        $this->ecrProtocol = $ecrProtocol;
        return $this;
    }

    /**
     * @return string
     */
    public function getContractStartDate(): string
    {
        return (string)$this->contractStartDate;
    }

    /**
     * @param string $contractStartDate
     * @return $this
     */
    public function setContractStartDate(string $contractStartDate): self
    {
        $this->contractStartDate = $contractStartDate;
        return $this;
    }

    /**
     * @return string
     */
    public function getContractEndDate(): string
    {
        return (string)$this->contractEndDate;
    }

    /**
     * @param string $contractEndDate
     * @return $this
     */
    public function setContractEndDate(string $contractEndDate): self
    {
        $this->contractEndDate = $contractEndDate;
        return $this;
    }

    /**
     * @return array
     */
    public function getPaymentTypes(): array
    {
        return $this->paymentTypes;
    }

    /**
     * @param array $paymentTypes
     * @return $this
     */
    public function setPaymentTypes(array $paymentTypes): self
    {
        $this->paymentTypes = $paymentTypes;
        return $this;
    }

    /**
     * @return array
     */
    public function getTerminalBrands(): array
    {
        return $this->terminalBrands;
    }

    /**
     * @param array $terminalBrands
     * @return $this
     */
    public function setTerminalBrands(array $terminalBrands): self
    {
        $this->terminalBrands = $terminalBrands;
        return $this;
    }

    /**
     * @return array
     */
    public function getLocation(): array
    {
        return $this->location;
    }

    /**
     * @param array $location
     * @return $this
     */
    public function setLocation(array $location): self
    {
        $this->location = $location;
        return $this;
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return (string)$this->createdAt;
    }

    /**
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt(string $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return string
     */
    public function getCreatedBy(): string
    {
        return (string)$this->createdBy;
    }

    /**
     * @param string $createdBy
     * @return $this
     */
    public function setCreatedBy(string $createdBy): self
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    /**
     * @return string
     */
    public function getModifiedAt(): string
    {
        return (string)$this->modifiedAt;
    }

    /**
     * @param string $modifiedAt
     * @return $this
     */
    public function setModifiedAt(string $modifiedAt): self
    {
        $this->modifiedAt = $modifiedAt;
        return $this;
    }

    /**
     * @return string
     */
    public function getModifiedBy(): string
    {
        return (string)$this->modifiedBy;
    }

    /**
     * @param string $modifiedBy
     * @return $this
     */
    public function setModifiedBy(string $modifiedBy): self
    {
        $this->modifiedBy = $modifiedBy;
        return $this;
    }

    /**
     * @return string
     */
    public function getDeletedAt(): string
    {
        return $this->deletedAt;
    }

    /**
     * @param string $deletedAt
     * @return $this
     */
    public function setDeletedAt(string $deletedAt): self
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }

    /**
     * @return string
     */
    public function getDeletedBy(): string
    {
        return $this->deletedBy;
    }

    /**
     * @param string $deletedBy
     * @return $this
     */
    public function setDeletedBy(string $deletedBy): self
    {
        $this->deletedBy = $deletedBy;
        return $this;
    }

    /**
     * @return string
     */
    public function get_links(): string
    {
        return $this->_links;
    }

    /**
     * @param string $_links
     * @return $this
     */
    public function set_links(string $_links): self
    {
        $this->_links = $_links;
        return $this;
    }

}
