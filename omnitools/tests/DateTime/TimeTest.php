<?php

namespace Keruald\OmniTools\Tests\DateTime;

use Keruald\OmniTools\DateTime\Time;
use PHPUnit\Framework\TestCase;

class TimeTest extends TestCase {

    private Time $time;

    protected function setUp () : void {
        $this->time = new Time(8, 6);
    }

    ///
    /// Tests
    ///

    public function testGetHours () : void {
        $this->assertSame(8, $this->time->getHours());
    }

    public function testGetMinutes () : void {
        $this->assertSame(6, $this->time->getMinutes());
    }

    public function testSetHours () : void {
        $this->time->setHours(9);
        $this->assertSame(9, $this->time->getHours());
    }

    public function testSetMinutes () : void {
        $this->time->setMinutes(7);
        $this->assertSame(7, $this->time->getMinutes());
    }

    public function testAddMinutes () : void {
        $this->time->addMinutes(7);
        $this->assertSame(13, $this->time->getMinutes());
    }

    public function testAddMinutesWithHourOverlap () : void {
        $this->time->addMinutes(55);
        $this->assertSame(9, $this->time->getHours());
        $this->assertSame(1, $this->time->getMinutes());
    }

    public function testAddMinutesWithDayOverlap () : void {
        $this->expectException(\OutOfRangeException::class);
        $this->time->addMinutes(2000);
    }

    public function testAddHours () : void {
        $this->time->addHours(5);
        $this->assertSame(13, $this->time->getHours());
    }

    public function testAddHoursWithDayOverlap () : void {
        $this->expectException(\OutOfRangeException::class);
        $this->time->addHours(16);
    }

    public function testToString () : void {
        $this->assertSame("08:06", (string)$this->time);
    }

    public function testFromMinutes () : void {
        $this->assertEquals($this->time, Time::fromMinutes(486));
    }

    public function testCompareTo () : void {
        $this->assertEquals(0, $this->time->compareTo(new Time(8, 6)));
        $this->assertEquals(-1, $this->time->compareTo(new Time(9, 0)));
        $this->assertEquals(1, $this->time->compareTo(new Time(3, 0)));
    }

    public function testParse () {
        $this->assertEquals($this->time, Time::Parse("08:06"));
        $this->assertEquals($this->time, Time::Parse("08:06:00"));
    }

}
