<?php

namespace PayNL\Sdk\Util;

class PayCache
{
    private string $cacheDir;
    private int $defaultTtl;
    private bool $enabled = true;

    /**
     * @param string|null $cacheDir
     * @param integer $defaultTtl
     */
    public function __construct(?string $cacheDir = null, int $defaultTtl = 600)
    {
        $this->cacheDir = $cacheDir ?? sys_get_temp_dir() . '/cache_pay_phpsdk';
        $this->defaultTtl = $defaultTtl;

        if (!is_dir($this->cacheDir)) {
            if (!@mkdir($this->cacheDir, 0777, true) && !is_dir($this->cacheDir)) {
                $this->enabled = false;
                return;
            }
        }

        if (!is_writable($this->cacheDir)) {
            $this->enabled = false;
        }
    }

    /**
     * @param string $key
     * @param callable|null $callback
     * @param integer|null $ttl
     * @return mixed|null
     */
    public function get(string $key, ?callable $callback = null, ?int $ttl = null): mixed
    {
        if (!$this->enabled) {
            return $callback ? $callback() : null;
        }

        $file = $this->getCacheFile($key);

        if (file_exists($file)) {
            $data = @unserialize(file_get_contents($file));

            if ($data !== false && isset($data['expires'], $data['value'])) {
                if ($data['expires'] >= time()) {
                    return $data['value'];
                }
            }

            # Cache expired or invalid
            @unlink($file);
        }

        if ($callback !== null) {
            $value = $callback();
            $this->set($key, $value, $ttl);
            return $value;
        }

        return null;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param integer|null $ttl
     * @return void
     */
    public function set(string $key, mixed $value,?int $ttl = null): void
    {
        if (!$this->enabled) {
            return;
        }

        $ttl = $ttl ?? $this->defaultTtl;
        $file = $this->getCacheFile($key);

        $data = [
            'expires' => time() + $ttl,
            'value' => $value
        ];

        @file_put_contents($file, serialize($data), LOCK_EX);
    }

    /**
     * @param string $key
     * @return void
     */
    public function delete(string $key): void
    {
        if (!$this->enabled) {
            return;
        }

        $file = $this->getCacheFile($key);
        if (file_exists($file)) {
            @unlink($file);
        }
    }

    /**
     * @return void
     */
    public function clear(): void
    {
        if (!$this->enabled) {
            return;
        }

        foreach (glob($this->cacheDir . '/*') ?: [] as $file) {
            @unlink($file);
        }
    }

    /**
     * @param string $key
     * @return string
     */
    private function getCacheFile(string $key): string
    {
        return $this->cacheDir . '/' . md5($key) . '.cache';
    }

    /**
     * @return boolean
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
