<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Tests\HTTP\Requests;

use Keruald\OmniTools\HTTP\Requests\RemoteAddress;
use PHPUnit\Framework\TestCase;

class RemoteAddressTest extends TestCase {

    ///
    /// Tests
    ///

    /**
     * @covers \Keruald\OmniTools\HTTP\Requests\RemoteAddress::getClientAddress
     * @covers \Keruald\OmniTools\HTTP\Requests\RemoteAddress::getAll
     * @covers \Keruald\OmniTools\HTTP\Requests\RemoteAddress::has
     */
    public function testEmptyRequest () : void {
        $address = new RemoteAddress;

        $this->assertEmpty($address->getClientAddress());
        $this->assertEmpty($address->getAll());
        $this->assertFalse($address->has());
    }

    /**
     * @covers \Keruald\OmniTools\HTTP\Requests\RemoteAddress::getClientAddress
     * @dataProvider provideTenZeroZeroThreeHeaderValues
     */
    public function testGetOne (string $value) : void {
        $address = new RemoteAddress($value);

        $this->assertEquals('10.0.0.3', $address->getClientAddress());
    }

    /**
     * @covers \Keruald\OmniTools\HTTP\Requests\RemoteAddress::getClientAddress
     * @dataProvider provideTenZeroZeroThreeHeaderValues
     */
    public function testGetAll (string $value) : void {
        $address = new RemoteAddress($value);

        $this->assertEquals($value, $address->getAll());
    }

    /**
     * @covers \Keruald\OmniTools\HTTP\Requests\RemoteAddress::has
     * @dataProvider provideTenZeroZeroThreeHeaderValues
     */
    public function testHas (string $value) : void {
        $address = new RemoteAddress($value);

        $this->assertTrue($address->has());
    }

    ///
    /// Data provider
    ///

    public function provideTenZeroZeroThreeHeaderValues () : iterable {
        return [
            // Each value should return 10.0.0.3
            ['10.0.0.3'],
            ['10.0.0.3,10.0.0.4'],
            ['10.0.0.3, 10.0.0.4'],
            ['10.0.0.3, 10.0.0.4, lorem ipsum dolor'],
        ];
    }

}
