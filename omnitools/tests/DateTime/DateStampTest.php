<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Tests\DateTime;

use Keruald\OmniTools\DateTime\DateStamp;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use DateTime;

class DateStampTest extends TestCase {

    ///
    /// Private members
    ///

    /**
     * @var DateStamp
     */
    private $dateStamp;

    ///
    /// Fixtures
    ///

    protected function setUp () : void {
        $this->dateStamp = new DateStamp(2010, 11, 25); // 25 November 2010
    }

    ///
    /// Tests
    ///

    public function testToUnixTime () : void {
        $this->assertEquals(1290643200, $this->dateStamp->toUnixTime());
    }

    public function testToDateTime () : void {
        $expectedDateTime = new DateTime("2010-11-25");

        $this->assertEquals($expectedDateTime, $this->dateStamp->toDateTime());
    }

    public function testToShortString () : void {
        $this->assertEquals("20101125", $this->dateStamp->toShortString());
    }

    public function testToString () : void {
        $this->assertEquals("2010-11-25", (string)$this->dateStamp);
    }

    public function testFromUnixTime () : void {
        $this->assertEquals(
            $this->dateStamp,
            DateStamp::fromUnixTime(1290643200)
        );
    }

    public function testParse () : void {
        $this->assertEquals(
            $this->dateStamp,
            DateStamp::parse("2010-11-25")
        );

        $this->assertEquals(
            $this->dateStamp,
            DateStamp::parse("20101125")
        );
    }

    #[DataProvider('provideInvalidDateStamps')]
    public function testParseWithInvalidFormat ($dateStamp) : void {
        $this->expectException("InvalidArgumentException");
        DateStamp::parse($dateStamp);
    }

    ///
    /// Data provider
    ///

    public static function provideInvalidDateStamps () : iterable {
        yield ["10-11-25"];
        yield ["2010-41-25"];
        yield ["2010-11-99"];
        yield ["20104125"];
        yield ["20101199"];
    }

}
