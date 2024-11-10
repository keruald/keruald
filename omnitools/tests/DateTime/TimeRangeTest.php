<?php

namespace Keruald\OmniTools\Tests\DateTime;

use Keruald\OmniTools\DateTime\Time;
use Keruald\OmniTools\DateTime\TimeRange;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TimeRangeTest extends TestCase {

    private Time $start;

    private Time $end;

    private TimeRange $range;

    protected function setUp () : void {
        $this->start = new Time(8, 0);
        $this->end = new Time(11, 36);

        $this->range = new TimeRange($this->start, $this->end);
    }

    public function testGetStart () {
        $this->assertEquals($this->start, $this->range->getStart());
    }

    public function testGetEnd () {
        $this->assertEquals($this->end, $this->range->getEnd());
    }

    public function testNormalize () {
        $range = new TimeRange($this->end, $this->start);
        $this->assertEquals($this->start, $range->getStart());
    }

    public function testParse () {
        $range = TimeRange::Parse("08:00-11:36");
        $this->assertEquals($this->start, $range->getStart());
        $this->assertEquals($this->end, $range->getEnd());
    }

    public function testFromDuration () {
        $this->assertEquals(
            $this->end,
            TimeRange::fromDuration($this->start, 3, 36)->getEnd()
        );
    }

    public function testDuration () {
        $this->assertEquals(216, $this->range->countMinutes());
    }

    #[DataProvider('provideOverlappingRanges')]
    public function testOverlap ($range1, $range2, $isOverlap) {
        $this->assertEquals(
            $isOverlap,
            TimeRange::parse($range1)->overlapsWith(TimeRange::parse($range2))
        );
    }

    ///
    /// Data providers
    ///

    public static function provideOverlappingRanges() : iterable {

        /**
         * Two time ranges are overlapping if, THEIR LIMITS EXCLUDED,
         * they don't have a common element.
         *
         * ]start1, end1[ ∩ ]start2, end2[ ≠ ø
         */

        yield ["09:00-11:00", "09:00-11:00", true];
        yield ["09:00-11:00", "10:00-12:00", true];
        yield ["10:00-12:00", "09:00-11:00", true];
        yield ["09:00-12:00", "10:00-11:00", true];
        yield ["10:00-11:00", "09:00-12:00", true];
        yield ["09:00-10:00", "11:00-12:00", false];
        yield ["11:00-12:00", "09:00-10:00", false];
        yield ["09:00-10:00", "10:00-11:00", false];
        yield ["10:00-11:00", "09:00-10:00", false];
        yield ["10:00-10:00", "09:00-11:00", true];
        yield ["09:00-11:00", "10:00-10:00", true];
        yield ["09:00-09:00", "09:00-10:00", false];
        yield ["10:00-10:00", "09:00-10:00", false];
        yield ["09:00-10:00", "09:00-09:00", false];
        yield ["09:00-10:00", "10:00-10:00", false];
        yield ["09:00-09:00", "09:00-09:00", false];
    }

}
