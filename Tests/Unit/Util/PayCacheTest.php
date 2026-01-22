<?php

declare(strict_types=1);

use PayNL\Sdk\Util\PayCache;
use PHPUnit\Framework\TestCase;

final class PayCacheTest extends TestCase
{
    private function invokeMethod(object $object, string $method, array $args = []): mixed
    {
        $refClass = new \ReflectionClass($object);
        $refMethod = $refClass->getMethod($method);
        $refMethod->setAccessible(true);

        return $refMethod->invokeArgs($object, $args);
    }

    public function testIsEnabledIsTrueByDefault(): void
    {
        $cacheDir = sys_get_temp_dir() . '/paynl-cache-test';

        $cache = new PayCache($cacheDir, 600);

        $this->assertTrue($cache->isEnabled());
    }

    public function testGetCacheFileBuildsExpectedPath(): void
    {
        $cacheDir = sys_get_temp_dir() . '/paynl-cache-test';
        $key      = 'my-cache-key';

        $cache = new PayCache($cacheDir, 600);

        /** @var string $file */
        $file = $this->invokeMethod($cache, 'getCacheFile', [$key]);

        $expected = $cacheDir . '/' . md5($key) . '.cache';

        $this->assertSame($expected, $file);
    }
}
