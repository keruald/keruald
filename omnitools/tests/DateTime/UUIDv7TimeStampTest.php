<?php

namespace Keruald\OmniTools\Tests\DateTime;

use DateTime;
use InvalidArgumentException;

use Keruald\OmniTools\Collections\BitsVector;
use Keruald\OmniTools\DateTime\UUIDv7TimeStamp;
use PHPUnit\Framework\TestCase;

class UUIDv7TimeStampTest extends TestCase {

    const MAX_48 = 2**48 - 1;

    public function testFromUUIDv7 () {
        // UUID example from draft-peabody-dispatch-new-uuid-format-03 B.2
        $uuid = "017F21CF-D130-7CC3-98C4-DC0C0C07398F";

        $expected = BitsVector::fromInteger(0x017F21CFD130)
                              ->shapeCapacity(48);

        $timestamp = UUIDv7TimeStamp::fromUUIDv7($uuid);
        self::assertEquals(
            $expected->toArray(),
            $timestamp->toBitsVector()->toArray(),
        );
    }

    public function testFromBits () {
        $bits = BitsVector::fromInteger(0x017F21CFD130)
                          ->shapeCapacity(48);
        $timestamp = UUIDv7TimeStamp::fromBits($bits);

        $this->assertEquals(48, $timestamp->toBitsVector()->count());
        $this->assertSame(
            $bits->toArray(),
            $timestamp->toBitsVector()->toArray()
        );
    }

    public function testFromBitsWithWrongNumber () {
        $this->expectException(InvalidArgumentException::class);

        $bits = BitsVector::new(0); // too small, we need 48
        UUIDv7TimeStamp::fromBits($bits);
    }
    public function testFromIntegerWith48Bits() {
        $timestamp = UUIDv7TimeStamp::fromInteger(self::MAX_48);

        $expected = array_fill(0, 48, 1);
        $this->assertEquals($expected, $timestamp->toBitsVector()->toArray());

    }
    public function testFromIntegerWithTruncatedPrecision() {
        $timestamp = UUIDv7TimeStamp::fromInteger(PHP_INT_MAX);

        $expected = array_fill(0, 48, 1);
        $this->assertSame($expected, $timestamp->toBitsVector()->toArray());
    }

    public function testFromDateTime() {
        $time = DateTime::createFromFormat(
            "Y-m-d H:i:s",
            '2022-02-22 14:22:22'
        );
        $timestamp = UUIDv7TimeStamp::fromDateTime($time);

        $actual = $timestamp->toBitsVector()->toInteger();
        $this->assertEquals(0x017F21CFD130, $actual);
    }

    public function testToUnixTime() {
        $time = time();

        $actual = UUIDv7TimeStamp::fromUnixTime($time)
                                 ->toBitsVector()
                                 ->toInteger() / 1000;
        $this->assertEquals($time, $actual);
    }

}
