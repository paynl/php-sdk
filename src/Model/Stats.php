<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model;

use Cassandra\Exception\ProtocolException;
use Exception;
use JsonSerializable;
use PayNL\Sdk\Common\JsonSerializeTrait;

/**
 * Class Statistics
 *
 * @package PayNL\Sdk\Model
 */
class Stats implements
    ModelInterface,
    JsonSerializable
{
    use JsonSerializeTrait;


    /**
     * @var string
     */
    protected $info;

    /**
     * @var string
     */
    protected $tool;

    /**
     * @var string|null
     */
    protected ?string $object = null;

    /**
     * @var string
     */
    protected string $extra1 = '';

    protected string $extra2 = '';

    /**
     * @var string
     */
    protected $extra3;

    protected $domainId;

    /**
     * @return string
     */
    public function getObject(): string
    {
        return (string)$this->object;
    }

    /**
     * @param string $object
     *
     * @return Stats
     */
    public function setObject(string $object): self
    {
        $this->object = $object;
        return $this;
    }

    /**
     * @return string
     */
    public function getInfo(): string
    {
        return (string)$this->info;
    }

    /**
     * @param string $info
     *
     * @return Stats
     */
    public function setInfo(string $info): self
    {
        $this->info = $info;
        return $this;
    }

    /**
     * @return string
     */
    public function getTool(): string
    {
        return (string)$this->tool;
    }

    /**
     * @param string $tool
     *
     * @return Stats
     */
    public function setTool(string $tool): self
    {
        $this->tool = $tool;
        return $this;
    }

    /**
     * @return string
     */
    public function getExtra1(): string
    {
        return (string)$this->extra1;
    }

    /**
     * @param string $extra1
     *
     * @return Stats
     */
    public function setExtra1(string $extra1): self
    {
        $this->extra1 = $extra1;
        return $this;
    }

    /**
     * @return string
     */
    public function getExtra2(): string
    {
        return (string)$this->extra2;
    }

    /**
     * @param string $extra2
     *
     * @return Stats
     */
    public function setExtra2(string $extra2): self
    {
        $this->extra2 = $extra2;
        return $this;
    }

    /**
     * @return string
     */
    public function getExtra3(): string
    {
        return (string)$this->extra3;
    }

    /**
     * @param string $extra3
     *
     * @return Stats
     */
    public function setExtra3(string $extra3): self
    {
        $this->extra3 = $extra3;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDomainId()
    {
        return $this->domainId;
    }

    /**
     * @param $domainId
     * @return $this
     * @throws Exception
     */
    public function setDomainId($domainId): self
    {
        $this->domainId = strtoupper($domainId);

        if (substr($this->domainId, 0, 2) != 'WU') {
            throw new Exception('domainId should start with `WU`');
        }

        return $this;
    }
}
