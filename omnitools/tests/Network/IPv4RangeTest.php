<?php

namespace Keruald\OmniTools\Tests\Network;

use Keruald\OmniTools\Network\IPRange;
use PHPUnit\Framework\TestCase;

class IPv4RangeTest extends TestCase {

    /**
     * @var IPRange
     */
    protected $range;

    ///
    /// Fixtures
    ///

    protected function setUp () : void {
        $this->range = IPRange::from("216.66.0.0/18");
    }

    ///
    /// Tests
    ///

    public function testGetBase () : void {
        $this->assertEquals("216.66.0.0", $this->range->getBase());
    }

    public function testGetNetworkBits () : void {
        $this->assertEquals(18, $this->range->getNetworkBits());
    }

    public function testCount () : void {
        $this->assertEquals(14, $this->range->count()); // 14 + 18 = 32 bits
    }

    public function testGetFirst () : void {
        $this->assertEquals("216.66.0.0", $this->range->getFirst());
    }

    public function testGetLast () : void {
        $this->assertEquals("216.66.63.255", $this->range->getLast());
    }

}
