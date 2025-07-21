<?php

declare(strict_types=1);

namespace PayNL\Sdk\Config;

use Countable;
use Iterator;
use ArrayAccess;

/**
 * Class Config
 *
 * @package PayNL\Sdk
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class Config implements Countable, Iterator, ArrayAccess
{
    public const TGU1 = 'https://connect.pay.nl';
    public const TGU2 = 'https://connect.payments.nl';
    public const TGU3 = 'https://connect.achterelkebetaling.nl';

    protected array $data = [];
    private static Config $configObject;
    private string $serviceId;

    /**
     * Config constructor.
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            if (true === is_array($value)) {
                $value = new self($value);
            }
            $this->data[$key] = $value;
        }
    }

    /**
     * @return void
     */
    public function __clone()
    {
        $data = [];

        foreach ($this->data as $key => $value) {
            if ($value instanceof self) {
                $value = clone $value;
            }
            $data[$key] = $value;
        }

        $this->data = $data;
    }

    /**
     * @param mixed $key
     * @param mixed $default
     * @return mixed|null
     */
    public function get(mixed $key, mixed $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * @param string|integer $key
     * @return mixed|null
     */
    public function __get(string|int $key)
    {
        return $this->get($key);
    }

    /**
     * @param mixed $key
     * @param mixed $value
     * @return void
     */
    public function set(mixed $key, mixed $value): void
    {
        if (true === is_array($value)) {
            $value = new self($value);
        }

        $this->data[$key] = $value;
    }

    /**
     * @param mixed $key
     * @param mixed $value
     * @return void
     */
    public function __set(mixed $key, mixed $value): void
    {
        $this->set($key, $value);
    }

    /**
     * @param string|integer $key
     *
     * @return void
     */
    public function remove(string|int $key): void
    {
        if (true === $this->has($key)) {
            unset($this->data[$key]);
        }
    }

    /**
     * @param string|integer $key
     *
     * @return void
     */
    public function __unset(string|int $key): void
    {
        $this->remove($key);
    }

    /**
     * @param string|integer $key
     *
     * @return boolean
     */
    public function has(string|int $key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * @param string|integer $key
     *
     * @return boolean
     */
    public function __isset(string|int $key): bool
    {
        return $this->has($key);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $array = [];
        $data = $this->data;

        foreach ($data as $key => $value) {
            if ($value instanceof self) {
                $value = $value->toArray();
            }
            $array[$key] = $value;
        }

        return $array;
    }

    /**
     * @return mixed
     */
    public function current(): mixed
    {
        return current($this->data);
    }


    /**
     * @inheritDoc
     *
     * @return void
     */
    public function next(): void
    {
        next($this->data);
    }

    /**
     * @return mixed
     */
    public function key(): mixed
    {
        return key($this->data);
    }

    /**
     * @inheritDoc
     * @return boolean
     */
    public function valid(): bool
    {
        return null !== $this->key();
    }

    /**
     * @inheritDoc
     *
     * @return void
     */
    public function rewind(): void
    {
        reset($this->data);
    }

    /**
     * @param mixed $offset
     * @return boolean
     */
    public function offsetExists(mixed $offset): bool
    {
        return $this->has($offset);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set($offset, $value);
    }

    /**
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset(mixed $offset): void
    {
        $this->remove($offset);
    }

    /**
     * @return integer
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * Merge the current config object with the given one
     *
     * @param Config $mergeConfig
     *
     * @return Config
     */
    public function merge(Config $mergeConfig): self
    {
        foreach ($mergeConfig as $key => $value) {
            $currentValue = $this->get($key);
            if ($value instanceof self && $currentValue instanceof self) {
                $value = $currentValue->merge($value);
            }
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    $currentValue[$k] = $v;
                }
                $value = $currentValue;
            }
            $this->set($key, $value);
        }
        return $this;
    }

    /**
     * @param boolean $debug
     * @return $this
     */
    public function setDebug(bool $debug): self
    {
        $this->data['debug'] = $debug;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getDebug(): bool
    {
        return $this->data['debug'] == 1;
    }

    /**
     * @param string $url
     * @return self
     */
    public function setupFailoverUrl(string $url): self
    {
        $this->data['failoverUrl'] = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getFailoverUrl(): string
    {
        if (!empty($this->data['failoverUrl'])) {
            return trim($this->data['failoverUrl']);
        }
        return '';
    }

    /**
     * Set destination(core) url
     * @param string $url
     * @return $this
     */
    public function setCore(string $url): self
    {
        if (!empty($url)) {
            $this->data['api']['url'] = $url;
        }
        return $this;
    }

    /**
     * Set version of API URL
     *
     * @param integer $version
     * @return $this
     */
    public function setVersion(int $version): self
    {
        $this->data['api']['version'] = $version;
        return $this;
    }

    /**
     * @return string
     */
    public function getCore(): string
    {
        return $this->data['api']['url'] ?? '';
    }

    /**
     * @param string $username
     * @return $this
     */
    public function setUsername(string $username): self
    {
        $this->data['authentication']['username'] = trim($username);
        return $this;
    }

    /**
     * @return boolean
     */
    public function isEmpty()
    {
        if (
            empty($this->data['authentication']['password']) ||
            empty($this->data['authentication']['username'])
        ) {
            return true;
        }
        return false;
    }

    /**
     * @return boolean
     */
    public function isCacheEnabled()
    {
        return ($this->data['useFileCaching'] ?? 0) == 1;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->data['authentication']['username'] ?? '';
    }

    /**
     * @param string $password
     * @return $this
     */
    public function setPassword(string $password): self
    {
        $this->data['authentication']['password'] = trim($password);
        return $this;
    }

    /**
     * @param string $serviceId
     * @return $this
     */
    public function setServiceId(string $serviceId): self
    {
        $this->serviceId = trim($serviceId);
        return $this;
    }

    /**
     * @return string
     */
    public function getServiceId(): string
    {
        return (string)$this->serviceId;
    }

    /**
     * @param boolean $caching
     * @return self
     */
    public function setCaching(bool $caching): self
    {
        $this->data['useFileCaching'] = $caching;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->data['authentication']['password'] ?? '';
    }

    /**
     * Get global config
     *
     * @return Config
     */
    public static function getConfig()
    {
        if (empty(self::$configObject)) {
            self::$configObject = (new Config(require __DIR__ . '/../../config/config.global.php'));
        }
        return self::$configObject;
    }
}
