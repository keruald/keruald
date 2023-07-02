<?php

namespace Keruald\Cache\Tests\Engines;

use Keruald\Cache\Engines\CacheMemcached;
use Keruald\OmniTools\Collections\HashMap;

use Keruald\OmniTools\Network\SocketAddress;
use PHPUnit\Framework\TestCase;

use Memcached;

class CacheMemcachedTest extends TestCase {

    private CacheMemcached $cache;

    protected function setUp () : void {

        if (!extension_loaded("memcached")) {
            $this->markTestSkipped("Memcached extension is required to test.");
        }

        if (!SocketAddress::from("127.0.0.1", 11211)->isOpen()) {
            $this->markTestSkipped("Memcached server can't be reached.");
        }

        $memcached = new Memcached();
        $memcached->addServer("127.0.0.1", 11211);

        $this->cache = new CacheMemcached($memcached);
    }

    public function testSet () {
        $result = $this->cache->set("foo", "bar");

        $this->assertTrue($result);
    }

    public function testGet () {
        $this->cache->set("foo", "bar");

        $this->assertEquals("bar", $this->cache->get("foo"));
    }

    public function testHas () {
        $result = $this->cache->set("foo", "bar");

        $this->assertTrue($this->cache->has("foo"));
    }

    public function testDelete () {
        $this->cache->set("foo", "bar");
        $result = $this->cache->delete("foo");

        $this->assertTrue($result);
    }

    public function testClear () {
        $result = $this->cache->clear();

        $this->assertTrue($result);
    }

    public function testGetMultiple () {
        $expected = [
            "foo" => "bar",
            "bar" => "baz",
        ];

        $this->cache->set("foo", "bar");
        $this->cache->set("bar", "baz");

        $results = $this->cache->getMultiple(["foo", "bar"]);
        $results = HashMap::from($results)->toArray();

        $this->assertEquals($expected, $results);
    }

    public function testDeleteMultiple () {
        $this->cache->set("foo", "bar");
        $this->cache->set("bar", "baz");

        $result = $this->cache->deleteMultiple(["foo", "bar"]);

        $this->assertTrue($result);
    }

    public function testSetMultiple () {
        $result = $this->cache->setMultiple([
            "foo" => "bar",
            "bar" => "baz",
        ]);

        $this->assertTrue($result);
    }

    public function testLoad () {
        $cache = CacheMemcached::load([]);

        $this->assertInstanceOf(CacheMemcached::class, $cache);
    }
}
