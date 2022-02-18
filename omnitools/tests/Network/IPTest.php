<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Tests\Network;

use Keruald\OmniTools\Network\IP;
use PHPUnit\Framework\TestCase;

class IPTest extends TestCase {

    ///
    /// Data providers for IP addresses
    ///
    /// These data providers methods allow providing IP addresses
    /// to validate or invalidate.
    ///
    /// The advanced IPv6 tests have been curated by Stephen Ryan
    /// Source: https://web.archive.org/web/20110515134717/http://forums.dartware.com/viewtopic.php?t=452
    ///

    public function provideValidIP () : iterable {
        yield ["0.0.0.0"];
        yield ["17.17.17.17"];
        yield ["fe80:0000:0000:0000:0204:61ff:fe9d:f156"];
    }

    public function provideInvalidIP () : iterable {
        yield [""];
        yield ["1"];
        yield ["17.17"];
        yield ["17.17.17.256"];
    }

    public function provideValidIPv4 () : iterable {
        return [["0.0.0.0"], ["17.17.17.17"]];
    }

    public function provideInvalidIPv4 () : iterable {
        yield [""];
        yield ["1"];
        yield ["17.17"];
        yield ["17.17.17.256"];
        yield ["fe80:0000:0000:0000:0204:61ff:fe9d:f156"];
    }

    public function provideValidIPv6 () : iterable {
        yield ["::1"];
        yield ["::ffff:192.0.2.128"];
        yield ["fe80:0000:0000:0000:0204:61ff:fe9d:f156"];

        yield ["::ffff:192.0.2.128", "IPv4 represented as dotted-quads"];
    }

    public function provideInvalidIPv6 () : iterable {
        yield ["0.0.0.0"];
        yield [""];
        yield ["1"];
        yield ["17.17"];
        yield ["17.17.17.17"];
        yield ["::fg", "Valid IPv6 digits are 0-f, ie 0-9 and a-f"];

        yield ["02001:0000:1234:0000:0000:C1C0:ABCD:0876", "Extra 0"];
        yield ["2001:0000:1234:0000:00001:C1C0:ABCD:0876", "Extra 0"];
        yield ["1.2.3.4:1111:2222:3333:4444::5555"];
    }

    public function provideValidLoopbackIP () : iterable {
        yield ["127.0.0.1"];
        yield ["127.0.0.3"];
        yield ["::1"];
    }

    public function provideInvalidLoopbackIP () : iterable {
        yield ["0.0.0.0"];
        yield ["1.2.3.4"];
        yield ["192.168.1.1"];
        yield ["::2"];
    }

    ///
    /// Test cases
    ///

     /**
     * @covers \Keruald\OmniTools\Network\IP::isIP
     * @dataProvider provideValidIP
     */
    public function testIsIP ($ip) {
        $this->assertTrue(IP::isIP($ip));
    }

    /**
     * @covers \Keruald\OmniTools\Network\IP::isIP
     * @dataProvider provideInvalidIP
     */
    public function testIsIPWhenItIsNot ($ip) {
        $this->assertFalse(IP::isIP($ip));
    }

    /**
     * @covers \Keruald\OmniTools\Network\IP::isIPv4
     * @dataProvider provideValidIPv4
     */
    public function testIsIPv4 ($ip) {
        $this->assertTrue(IP::isIPv4($ip));
    }

    /**
     * @covers \Keruald\OmniTools\Network\IP::isIPv4
     * @dataProvider provideInvalidIPv4
     */
    public function testIsIPv4WhenItIsNot ($ip) {
        $this->assertFalse(IP::isIPv4($ip));
    }

    /**
     * @covers \Keruald\OmniTools\Network\IP::isIPv6
     * @dataProvider provideValidIPv6
     */
    public function testIsIPv6 (string $ip, string $message = "") {
        $this->assertTrue(IP::isIPv6($ip), $message);
    }

    /**
     * @covers \Keruald\OmniTools\Network\IP::isIPv6
     * @dataProvider provideInvalidIPv6
     */
    public function testIsIPv6WhenItIsNot (string $ip, string $message = "") : void {
        $this->assertFalse(IP::isIPv6($ip), $message);
    }

    /**
     * @covers \Keruald\OmniTools\Network\IP::isLoopback
     * @dataProvider provideValidLoopbackIP
     */
    public function testIsLoopback (string $ip) : void {
        $this->assertTrue(IP::isLoopback($ip));
    }

    /**
     * @covers \Keruald\OmniTools\Network\IP::isLoopback
     * @dataProvider provideInvalidLoopbackIP
     */
    public function testIsLoopbackWhenItIsNot (string $ip) : void {
        $this->assertFalse(IP::isLoopback($ip));
    }

}
