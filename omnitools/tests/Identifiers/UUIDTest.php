<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Tests\Identifiers;

use InvalidArgumentException;

use Keruald\OmniTools\Collections\BitsVector;
use Keruald\OmniTools\Collections\Vector;
use Keruald\OmniTools\DateTime\UUIDv1TimeStamp;
use Keruald\OmniTools\Identifiers\UUID;
use Phpunit\Framework\TestCase;

class UUIDTest extends TestCase {

    public function testUUIDv1 () : void {
        $uuid = UUID::UUIDv1();

        $this->assertEquals(
            36, strlen($uuid),
            "UUID size must be 36 characters."
        );

        $re = "/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/";
        $this->assertMatchesRegularExpression($re, $uuid);

        $this->assertEquals(1, UUID::getVersion($uuid));
    }

    public function testUUIDV1WithMac () : void {
        $uuid = UUID::UUIDv1("00-00-5E-00-53-00");

        $macFromUUID = BitsVector::fromDecoratedHexString($uuid)
                                 ->slice(80, 48)
                                 ->toBytesArray();

        $this->assertSame([0, 0, 0x5E, 0, 0x53, 0], $macFromUUID);
    }

    public function testUUIDv1FromValuesWithBadCount () : void {
        $this->expectException(InvalidArgumentException::class);

        $node = BitsVector::new(0); // too small, must be 48
        UUID::UUIDv1FromValues(UUIDv1TimeStamp::now(), 0, 0, $node);
    }

    public function testUUIDv4 () : void {
        $uuid = UUID::UUIDv4();

        $this->assertEquals(
            36, strlen($uuid),
            "UUID size must be 36 characters."
        );

        $re = "/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/";
        $this->assertMatchesRegularExpression($re, $uuid);
    }

    public function testUUIDv4WithoutHyphens () : void {
        $uuid = UUID::UUIDv4WithoutHyphens();

        $this->assertEquals(
            32, strlen($uuid),
            "UUID size must be 36 characters, and there are 4 hyphens, so here 32 characters are expected."
        );

        $re = "/[0-9a-f]/";
        $this->assertMatchesRegularExpression($re, $uuid);
    }

    public function testUUIDv4AreUnique () : void {
        $this->assertNotEquals(UUID::UUIDv4(), UUID::UUIDv4());
    }

    public function testUUIDv6 () : void {
        $uuid = UUID::UUIDv6();

        $this->assertEquals(
            36, strlen($uuid),
            "UUID size must be 36 characters."
        );

        $re = "/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/";
        $this->assertMatchesRegularExpression($re, $uuid);

        $this->assertEquals(6, UUID::getVersion($uuid));
    }

    public function testUUIDV6WithMac () : void {
        $uuid = UUID::UUIDv6("00-00-5E-00-53-00");

        $macFromUUID = BitsVector::fromDecoratedHexString($uuid)
                                 ->slice(80, 48)
                                 ->toBytesArray();

        $this->assertSame([0, 0, 0x5E, 0, 0x53, 0], $macFromUUID);
    }

    public function testUUIDv6FromValuesWithBadCount () : void {
        $this->expectException(InvalidArgumentException::class);

        $node = BitsVector::new(0); // too small, must be 48
        UUID::UUIDv6FromValues(UUIDv1TimeStamp::now(), 0, 0, $node);
    }

    public function testUUIDv8 () : void {
        $this->assertEquals(
            "320c3d4d-cc00-875b-8ec9-32d5f69181c0",
            UUID::UUIDv8(0x320C3D4DCC00, 0x75B, 0xEC932D5F69181C0)
        );
    }

    public function provideUUIDV8OverflowValues () : iterable {
        yield [PHP_INT_MAX, 0x75B, 0xEC932D5F69181C0];
        yield [0x320C3D4DCC00, PHP_INT_MAX, 0xEC932D5F69181C0];
        yield [0x320C3D4DCC00, 0x75B, PHP_INT_MAX];
    }

    /**
     * @dataProvider provideUUIDV8OverflowValues
     */
    public function testUUIDV8WithOverflowValues ($a, $b, $c) : void {
        $this->expectException(InvalidArgumentException::class);
        UUID::UUIDv8($a, $b, $c);
    }


    public function testUUIDv7FromValues () : void {
        $this->assertEquals(
            "017f21cf-d130-7cc3-98c4-dc0c0c07398f",
            UUID::UUIDv7FromValues(0x017F21CFD130, 0xCC3, 0x18C4DC0C0C07398F)
        );
    }

    public function testUUIDv7 () : void {
        $uuid = UUID::UUIDv7();

        $this->assertEquals(
            36, strlen($uuid),
            "UUID size must be 36 characters."
        );

        $re = "/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/";
        $this->assertMatchesRegularExpression($re, $uuid);
    }

    public function testUUIDv7FromBitsWithBadCount () : void {
        $this->expectException(InvalidArgumentException::class);

        UUID::UUIDv7FromBits(BitsVector::new(0), 0xCC3, 0x18C4DC0C0C07398F);
    }

    ///
    /// Tests for convert between UUID methods
    ///

    public function testUUIDv1ToUUIDv6 () : void {
        $this->assertEquals(
            "1ec9414c-232a-6b00-b3c8-9e6bdeced846",
            UUID::UUIDv1ToUUIDv6("c232ab00-9414-11ec-b3c8-9e6bdeced846")
        );
    }

    public function testUUIDv6ToUUIDv1 () : void {
        $this->assertEquals(
            "c232ab00-9414-11ec-b3c8-9e6bdeced846",
            UUID::UUIDv6ToUUIDv1("1ec9414c-232a-6b00-b3c8-9e6bdeced846")
        );
    }

    ///
    /// Tests for helper methods
    ///

    public function provideFormattedUUID () : iterable {
        yield [
            "320c3d4dcc00875b8ec932d5f69181c0",
            "320c3d4d-cc00-875b-8ec9-32d5f69181c0",
        ];

        yield [
            "320c3d4d-cc00-875b-8ec9-32d5f69181c0",
            "320c3d4d-cc00-875b-8ec9-32d5f69181c0",
        ];

        yield [
            "320C3D4D-CC00-875B-8EC9-32D5F69181C0",
            "320c3d4d-cc00-875b-8ec9-32d5f69181c0",
        ];
    }

    /**
     * @dataProvider provideFormattedUUID
     */
    public function testReformat($uuidToReformat, $expected) {
        $this->assertEquals($expected, UUID::reformat($uuidToReformat));
    }

    public function testIsUUID () : void {
        $this->assertTrue(UUID::isUUID("e14d5045-4959-11e8-a2e6-0007cb03f249"));
        $this->assertFalse(
            UUID::isUUID("e14d5045-4959-11e8-a2e6-0007cb03f249c"),
            "The method shouldn't allow arbitrary size strings."
        );
        $this->assertFalse(UUID::isUUID("d825a90a27e7f161a07161c3a37dce8e"));
    }

    private function provideUUIDsWithVersionAndVariant () : iterable {
        // RFC 4122
        yield ["c232ab00-9414-11ec-b3c8-9e6bdeced846", 1, 2];
        yield ["f6244210-bbc3-3689-bb54-76528802d4d5", 3, 2];
        yield ["23b50a2e-0543-4eaa-a53f-2a9dd02606e7", 4, 2];
        yield ["2f8c2178-9c05-55ba-9b69-f4e076017270", 5, 2];

        // draft-peabody-dispatch-new-uuid-format-03
        yield ["1ec9414c-232a-6b00-b3c8-9e6bdeced846", 6, 2];
        yield ["018003e1-0e46-7c62-9e4e-63cda74165ea", 7, 2];
        yield ["320c3d4d-cc00-875b-8ec9-32d5f69181c0", 8, 2];

        // Special values from both RFC
        yield [UUID::NIL, 0, 0];
        yield [UUID::MAX, 15, 3];
    }

    /**
     * @dataProvider provideUUIDsWithVersionAndVariant
     */
    public function testGetVersion (string $uuid, int $version, int $variant) : void {
        $this->assertEquals($version, UUID::getVersion($uuid));
    }

    /**
     * @dataProvider provideUUIDsWithVersionAndVariant
     */
    public function testGetVariant (string $uuid, int $version, int $variant) : void {
        $this->assertEquals($variant, UUID::getVariant($uuid));
    }

    ///
    /// Monotonicity :: UUIDv6 UUIDv7
    ///

    private function assertMonotonicity (iterable $series) : void {
        $bigrams = Vector::from($series)->bigrams();
        foreach ($bigrams as $bigram) {
            $this->assertGreaterThan($bigram[0], $bigram[1]);
        }
    }

    public function testMonotonicityForUUIDv6 () {
        $series = Vector::range(0, 99)->map(fn($_) => UUID::UUIDv6());
        $this->assertMonotonicity($series);
    }

    public function testMonotonicityForSlowlyGeneratedUUIDv7 () {
        $series = Vector::range(0, 99)->map(function ($_) {
            usleep(1000);
            return UUID::UUIDv7();
        });
        $this->assertMonotonicity($series);
    }

    public function testMonotonicityForBatchesOfUUIDv7WhenBatchQuantityIsSmallEnough () {
        $series = UUID::batchOfUUIDv7(63);
        $this->assertMonotonicity($series);
    }

    public function testMonotonicityForBatchesOfUUIDv7 () {
        $series = UUID::batchOfUUIDv7(1000);

        $this->assertCount(1000, $series);
        $this->assertMonotonicity($series);
    }

}
