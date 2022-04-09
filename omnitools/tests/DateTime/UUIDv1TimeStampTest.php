<?php

namespace Keruald\OmniTools\Tests\DateTime;

use DateTime;
use InvalidArgumentException;

use Keruald\OmniTools\Collections\BitsVector;
use Keruald\OmniTools\DateTime\UUIDv1TimeStamp;
use PHPUnit\Framework\TestCase;

class UUIDv1TimeStampTest extends TestCase {

    public function testToUnixTime() {
        $time = time();

        $actual = UUIDv1TimeStamp::fromUnixTime($time)->toUnixTime();
        $this->assertEquals($time, $actual);
    }

    public function testFromBits() {
        $bits = BitsVector::fromInteger(0x1EC9414C232AB00)
                          ->shapeCapacity(60);
        $timestamp = UUIDv1TimeStamp::fromBits($bits);

        $this->assertEquals(1645557742, $timestamp->toUnixTime());
    }

    public function testFromUnixTime() {
        $timestamp = UUIDv1TimeStamp::fromUnixTime(1645557742);
        $this->assertEquals(1645557742, $timestamp->toUnixTime());
    }

    public function testFromDateTime() {
        $time = DateTime::createFromFormat(
            "Y-m-d H:i:s",
            '2022-02-22 19:22:22'
        );
        $timestamp = UUIDv1TimeStamp::fromDateTime($time);

        $this->assertEquals(1645557742, $timestamp->toUnixTime());
    }

    public function testFromBitsWhenCountIsWrong() {
        $this->expectException(InvalidArgumentException::class);

        $bits = BitsVector::new(0); // too small, we need 60
        UUIDv1TimeStamp::fromBits($bits);
    }

    public function testToBitsVector() {
        $expected = BitsVector::fromInteger(0x1EC9414C232AB00)
                              ->shapeCapacity(60);

        $timestamp = UUIDv1TimeStamp::fromUnixTime(1645557742);

        $this->assertEquals(
            $expected->toArray(),
            $timestamp->toBitsVector()->toArray(),
        );
    }

}
