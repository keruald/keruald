<?php

namespace Keruald\OmniTools\Tests\Network;

use Keruald\OmniTools\Network\IPRange;
use PHPUnit\Framework\TestCase;

class IPv6RangeTest extends TestCase {

    protected IPRange $range;

    ///
    /// Fixtures
    ///

    protected function setUp () : void {
        $this->range = IPRange::from("2001:400::/23");
    }

    ///
    /// Tests
    ///

    public function testGetBase () : void {
        $this->assertEquals("2001:400::", $this->range->getBase());
    }

    public function testGetNetworkBits () : void {
        $this->assertEquals(23, $this->range->getNetworkBits());
    }

    public function testCount () : void {
        $this->assertEquals(105, $this->range->count()); // 23 + 105 = 128 bits
    }

    public function testGetFirst () : void {
        $this->assertEquals("2001:400::", $this->range->getFirst());
    }

    public function testGetLast () : void {
        $this->assertEquals("2001:5ff:ffff:ffff:ffff:ffff:ffff:ffff", $this->range->getLast());
    }

    public function testContains () : void {
        $this->assertTrue($this->range->contains("2001:431::af"));
    }

    public function testContainsWorksWithIPv4MappedIPv6Address () : void {
        $this->assertTrue(IPRange::from("::ffff:0.0.0.0/96")->contains("1.2.3.4"));
    }

}
