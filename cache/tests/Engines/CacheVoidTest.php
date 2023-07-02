<?php

namespace Keruald\Cache\Tests\Engines;

use Keruald\Cache\Engines\CacheVoid;
use Keruald\OmniTools\Collections\HashMap;

use PHPUnit\Framework\TestCase;

class CacheVoidTest extends TestCase {

    private CacheVoid $cache;

    protected function setUp () : void {
        $this->cache = new CacheVoid;
    }

    public function testSet () {
        $result = $this->cache->set("foo", "bar");

        $this->assertTrue($result);
    }

    public function testDelete () {
        $result = $this->cache->delete("foo");

        $this->assertTrue($result);
    }

    public function testClear () {
        $result = $this->cache->clear();

        $this->assertTrue($result);
    }

    public function testGetMultiple () {
        $expected = [
            "foo" => null,
            "bar" => null,
        ];

        $results = $this->cache->getMultiple(["foo", "bar"]);
        $results = HashMap::from($results)->toArray();

        $this->assertEquals($expected, $results);
    }

    public function testDeleteMultiple () {
        $result = $this->cache->deleteMultiple(["foo", "bar"]);

        $this->assertTrue($result);
    }

    public function testGet () {
        $this->assertNull($this->cache->get("foo"));
    }

    public function testSetMultiple () {
        $result = $this->cache->setMultiple([
            "foo" => "bar",
            "bar" => "baz",
        ]);

        $this->assertTrue($result);
    }

    public function testHas () {
        $result = $this->cache->has("foo");

        $this->assertFalse($result);
    }

    public function testLoad () {
        $cache = CacheVoid::load([]);

        $this->assertInstanceOf(CacheVoid::class, $cache);
    }
}
