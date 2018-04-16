<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Tests\Network;

use Keruald\OmniTools\Network\IP;
use PHPUnit\Framework\TestCase;

class IPTest extends TestCase {

    /**
     * @covers \Keruald\OmniTools\Network\IP::isIP
     */
    function testIsIP () {
        $this->assertTrue(IP::isIP("0.0.0.0"));
        $this->assertFalse(IP::isIP(""));
        $this->assertFalse(IP::isIP("1"));
        $this->assertFalse(IP::isIP("17.17"));
        $this->assertTrue(IP::isIP("17.17.17.17"));
        $this->assertFalse(IP::isIP("17.17.17.256"));
        $this->assertTrue(IP::isIP("fe80:0000:0000:0000:0204:61ff:fe9d:f156"));
    }

    /**
     * @covers \Keruald\OmniTools\Network\IP::isIPv4
     */
    function testIsIPv4 () {
        $this->assertTrue(IP::isIPv4("0.0.0.0"));
        $this->assertFalse(IP::isIPv4(""));
        $this->assertFalse(IP::isIPv4("1"));
        $this->assertFalse(IP::isIPv4("17.17"));
        $this->assertTrue(IP::isIPv4("17.17.17.17"));
        $this->assertFalse(IP::isIPv4("17.17.17.256"));
        $this->assertFalse(IP::isIPv4(""));
        $this->assertFalse(IP::isIPv4("fe80:0000:0000:0000:0204:61ff:fe9d:f156"));
    }

    /**
     * @covers \Keruald\OmniTools\Network\IP::isIPv6
     */
    function testIsIPv6 () {
        $this->assertFalse(IP::isIPv6("0.0.0.0"));
        $this->assertFalse(IP::isIPv6(""));
        $this->assertFalse(IP::isIPv6("1"));
        $this->assertFalse(IP::isIPv6("17.17"));
        $this->assertFalse(IP::isIPv6("17.17.17.17"));
        $this->assertTrue(IP::isIPv6("::1"));
        $this->assertFalse(IP::isIPv6("::fg"));
        $this->assertTrue(IP::isIPv6("::1"));

        // Advanced IPv6 tests curated by Stephen Ryan
        // Source: http://forums.dartware.com/viewtopic.php?t=452
        $this->assertTrue(IP::isIPv6("fe80:0000:0000:0000:0204:61ff:fe9d:f156"));
        $this->assertFalse(IP::isIPv6("02001:0000:1234:0000:0000:C1C0:ABCD:0876"), "extra 0 not allowed");
        $this->assertFalse(IP::isIPv6("2001:0000:1234:0000:00001:C1C0:ABCD:0876"), "extra 0 not allowed");
        $this->assertFalse(IP::isIPv6("1.2.3.4:1111:2222:3333:4444::5555"));
        $this->assertTrue(IP::isIPv6("::ffff:192.0.2.128"), "can't validate IPv4 represented as dotted-quads");
    }

}
