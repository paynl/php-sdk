<?php

namespace PayNL\Sdk\Helpers;

trait StaticCacheTrait
{
    /**
     * In-memory static cache array
     */
    private static array $cache = [];

    /**
     * Get value from static cache, or execute callback and cache it.
     *
     * @param string $key
     * @param callable $callback
     * @return mixed
     * @throws \Exception
     */
    protected function staticCache(string $key, callable $callback): mixed
    {
        if (isset(self::$cache[$key])) {
            if (self::$cache[$key] instanceof \Exception) {
                throw self::$cache[$key];
            }
            return self::$cache[$key];
        }

        try {
            return self::$cache[$key] = $callback();
        } catch (\Exception $e) {
            self::$cache[$key] = $e;
            throw $e;
        }
    }

    /**
     * @param string $key
     * @return boolean
     */
    protected function hasStaticCache(string $key): bool
    {
        return isset(self::$cache[$key]);
    }

    /**
     * @param string $key
     * @return mixed
     * @throws \Exception
     */
    protected function getStaticCacheValue(string $key): mixed
    {
        if (!isset(self::$cache[$key])) {
            return null;
        }

        if (self::$cache[$key] instanceof \Exception) {
            throw self::$cache[$key];
        }

        return self::$cache[$key];
    }
}
