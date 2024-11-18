<?php

declare(strict_types=1);

namespace PayNL\Sdk\Config;

use Countable;
use Iterator;
use ArrayAccess;

if (PHP_VERSION_ID >= 80000) {
    /**
     * Class Config
     *
     * @package PayNL\Sdk
     *
     * @SuppressWarnings(PHPMD.TooManyPublicMethods)
     */
    class Config implements Countable, Iterator, ArrayAccess
    {
        const TGU1 = 'https://connect.pay.nl';
        const TGU2 = 'https://connect.payments.nl';
        const TGU3 = 'https://connect.achterelkebetaling.nl';

        protected array $data = [];
        private static Config $configObject;

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
         * @param $key
         * @param null $default
         */
        public function get($key, $default = null)
        {
            return $this->data[$key] ?? $default;
        }

        /**
         * @param string|int $key
         */
        public function __get($key)
        {
            return $this->get($key);
        }

        /**
         * @param string|int $key
         * @param $value
         *
         * @return void
         */
        public function set($key, $value): void
        {
            if (true === is_array($value)) {
                $value = new self($value);
            }

            $this->data[$key] = $value;
        }

        /**
         * @param string|int $key
         * @param $value
         *
         * @return void
         */
        public function __set($key, $value): void
        {
            $this->set($key, $value);
        }

        /**
         * @param string|int $key
         *
         * @return void
         */
        public function remove($key): void
        {
            if (true === $this->has($key)) {
                unset($this->data[$key]);
            }
        }

        /**
         * @param string|int $key
         *
         * @return void
         */
        public function __unset($key): void
        {
            $this->remove($key);
        }

        /**
         * @param string|int $key
         *
         * @return bool
         */
        public function has($key): bool
        {
            return isset($this->data[$key]);
        }

        /**
         * @param string|int $key
         *
         * @return bool
         */
        public function __isset($key): bool
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
         * @inheritDoc
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
         * @inheritDoc
         *
         * @return
         */
        public function key(): mixed
        {
            return key($this->data);
        }

        /**
         * @inheritDoc
         *
         * @return bool
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
         * @inheritDoc
         *
         * @return bool
         */
        public function offsetExists($offset): bool
        {
            return $this->has($offset);
        }

        /**
         * @inheritDoc
         *
         * @return
         */
        public function offsetGet($offset): mixed
        {
            return $this->get($offset);
        }

        /**
         * @inheritDoc
         *
         * @return void
         */
        public function offsetSet($offset, $value): void
        {
            $this->set($offset, $value);
        }

        /**
         * @inheritDoc
         *
         * @return void
         */
        public function offsetUnset($offset): void
        {
            $this->remove($offset);
        }

        /**
         * @inheritDoc
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
         * @param bool $debug
         * @return $this
         */
        public function setDebug(bool $debug): self
        {
            $this->data['debug'] = $debug;
            return $this;
        }

        /**
         * @return bool
         */
        public function getDebug(): bool
        {
            return $this->data['debug'] == 1;
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
         *
         * @param string $url
         * @return void
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
            $this->data['authentication']['username'] = $username;
            return $this;
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
            $this->data['authentication']['password'] = $password;
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
} else {
    /**
     * Class Config
     *
     * @package PayNL\Sdk
     *
     * @SuppressWarnings(PHPMD.TooManyPublicMethods)
     */
    class Config implements Countable, Iterator, ArrayAccess
    {
        const TGU1 = 'https://connect.pay.nl';
        const TGU2 = 'https://connect.payments.nl';
        const TGU3 = 'https://connect.achterelkebetaling.nl';

        protected array $data = [];
        private static Config $configObject;

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
         * @param $key
         * @param null $default
         */
        public function get($key, $default = null)
        {
            return $this->data[$key] ?? $default;
        }

        /**
         * @param string|int $key
         */
        public function __get($key)
        {
            return $this->get($key);
        }

        /**
         * @param string|int $key
         * @param $value
         *
         * @return void
         */
        public function set($key, $value): void
        {
            if (true === is_array($value)) {
                $value = new self($value);
            }

            $this->data[$key] = $value;
        }

        /**
         * @param string|int $key
         * @param $value
         *
         * @return void
         */
        public function __set($key, $value): void
        {
            $this->set($key, $value);
        }

        /**
         * @param string|int $key
         *
         * @return void
         */
        public function remove($key): void
        {
            if (true === $this->has($key)) {
                unset($this->data[$key]);
            }
        }

        /**
         * @param string|int $key
         *
         * @return void
         */
        public function __unset($key): void
        {
            $this->remove($key);
        }

        /**
         * @param string|int $key
         *
         * @return bool
         */
        public function has($key): bool
        {
            return isset($this->data[$key]);
        }

        /**
         * @param string|int $key
         *
         * @return bool
         */
        public function __isset($key): bool
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
         * @inheritDoc
         */
        public function current()
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
         * @inheritDoc
         *
         * @return
         */
        public function key()
        {
            return key($this->data);
        }

        /**
         * @inheritDoc
         *
         * @return bool
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
         * @inheritDoc
         *
         * @return bool
         */
        public function offsetExists($offset): bool
        {
            return $this->has($offset);
        }

        /**
         * @inheritDoc
         *
         * @return
         */
        public function offsetGet($offset)
        {
            return $this->get($offset);
        }

        /**
         * @inheritDoc
         *
         * @return void
         */
        public function offsetSet($offset, $value): void
        {
            $this->set($offset, $value);
        }

        /**
         * @inheritDoc
         *
         * @return void
         */
        public function offsetUnset($offset): void
        {
            $this->remove($offset);
        }

        /**
         * @inheritDoc
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
         * @param bool $debug
         * @return $this
         */
        public function setDebug(bool $debug): self
        {
            $this->data['debug'] = $debug;
            return $this;
        }

        /**
         * @return bool
         */
        public function getDebug(): bool
        {
            return $this->data['debug'] == 1;
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
         *
         * @param string $url
         * @return void
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
            $this->data['authentication']['username'] = $username;
            return $this;
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
            $this->data['authentication']['password'] = $password;
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
}

