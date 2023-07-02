<?php

namespace Keruald\Cache\Tests;

use Keruald\Cache\CacheFactory;
use Keruald\Cache\Engines\CacheVoid;

use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheException;

class CacheFactoryTest extends TestCase {

    public function testLoad () {
        $config = [
            "engine" => CacheDummy::class,
        ];
        $cache = CacheFactory::load($config);

        $this->assertInstanceOf(CacheDummy::class, $cache);
    }

    public function testLoadDefaultsToVoid () {
        $cache = CacheFactory::load([]);

        $this->assertInstanceOf(CacheVoid::class, $cache);
    }

    public function testLoadWithNonExistentClass () {
        $config = [
            "engine" => "Acme\\Nonexistent",
        ];

        $this->expectException(CacheException::class);
        CacheFactory::load($config);
    }

}
