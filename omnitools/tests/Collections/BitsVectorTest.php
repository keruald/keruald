<?php

namespace Keruald\OmniTools\Tests\Collections;

use InvalidArgumentException;
use OutOfRangeException;

use Keruald\OmniTools\Collections\BitsVector;
use PHPUnit\Framework\TestCase;

class BitsVectorTest extends TestCase {

    ///
    /// Constructors
    ///

    public function testConstructorWithInvalidIterable () : void {
        $this->expectException(InvalidArgumentException::class);
        new BitsVector([1, 2, 3, 4]);
    }

    public function testNew () : void {
        $bits = BitsVector::new(8);
        $this->assertEquals(8, $bits->count());
    }

    public function testNewWhenCapacityIsEmpty () : void {
        $bits = BitsVector::new(0);
        $this->assertEquals(0, $bits->count());
    }

    public function testNewWhenCapacityIsNegative () : void {
        $this->expectException(InvalidArgumentException::class);
        BitsVector::new(-8);
    }

    public function testFromInteger () : void {
        $bits = BitsVector::fromInteger(4);
        $this->assertEquals([1, 0, 0], $bits->toArray());
    }

    public function testFromIntegerWhenValueIsNegative () : void {
        $bits = BitsVector::fromInteger(-1);

        $expected = array_fill(0, 64, 1);
        $this->assertEquals($expected, $bits->toArray());
    }

    public function testFromBinString () : void {
        $bits = BitsVector::fromBinaryString("1001");
        $this->assertEquals([1, 0, 0, 1], $bits->toArray());
    }

    public function testFromHexString () : void {
        $bits = BitsVector::fromHexString("337362ea");
        $this->assertEquals("337362ea", $bits->toHexString());
    }

    public function testFromDecoratedHexString () : void {
        $bits = BitsVector::fromDecoratedHexString("cc4eca23-b825-11ec-ab20-a81e84f35d9c");
        $this->assertEquals("cc4eca23b82511ecab20a81e84f35d9c", $bits->toHexString());
    }

    public function testFromString () : void {
        // Exemple based on pack() documentation
        $binaryData = pack("nvc*", 0x1234, 0x5678, 65, 66);
        $bits = BitsVector::fromString($binaryData);

        $this->assertEquals(
            [0x12, 0x34, 0x78, 0x56, 0x41, 0x42],
            $bits->toBytesArray(),
        );
    }

    public function testBytesArray () : void {
        $bits = BitsVector::new(16)
            ->copyInteger(1, 0, 4)
            ->copyInteger(2, 4, 4)
            ->copyInteger(3, 8, 4)
            ->copyInteger(4, 12, 4);

        $this->assertEquals(
            [0x12, 0x34],
            $bits->toBytesArray(),
        );
    }

    public function provideLengths () : iterable {
        yield [1];
        yield [2];
        yield [8];

        yield [500];
        yield [5000];
        yield [0];
    }

    /**
     * @dataProvider provideLengths
     */
    public function testRandom($length) : void {
        $bits = BitsVector::random($length);
        $this->assertEquals($length, $bits->count());
    }

    public function testRandomWithNegativeLength() : void {
        $this->expectException(InvalidArgumentException::class);

        BitsVector::random(-1);
    }

    public function testBytesArrayWithBadLength () : void {
        $this->expectException(InvalidArgumentException::class);

        $bits = new BitsVector([1, 1, 1]); // 3 bits isn't a byte
        $bits->toBytesArray();
    }

    public function testToBinaryString () : void {
        $bits = new BitsVector([1, 0, 0, 1]);
        $this->assertEquals("1001", $bits->toBinaryString());
    }

    public function testToInteger () : void {
        $bits = new BitsVector([1, 0, 0, 1]);
        $this->assertEquals(9, $bits->toInteger());
    }

    public function testToIntegerWhenThereIsTooMuchBits () : void {
        $this->expectException(InvalidArgumentException::class);

        BitsVector::new(66)->toInteger();
    }

    public function testPad () : void {
        $bits = new BitsVector([1, 0, 0, 1]);
        $bits->pad(8);
        $this->assertEquals([0, 0, 0, 0, 1, 0, 0, 1], $bits->toArray());
    }

    public function testPadWithLargeEnoughCount () : void {
        $bits = new BitsVector([1, 0, 0, 1]);
        $bits->pad(4);
        $this->assertEquals([1, 0, 0, 1], $bits->toArray());
    }

    public function testTruncate () : void {
        $bits = new BitsVector([1, 0, 0, 1, 0, 0, 0, 0]);
        $bits->truncate(4);

        $this->assertEquals([1, 0, 0, 1], $bits->toArray());
    }

    public function testTruncateWithSmallEnoughCount () : void {
        $bits = new BitsVector([1, 0, 0, 1]);
        $bits->truncate(4);

        $this->assertEquals([1, 0, 0, 1], $bits->toArray());
    }

    public function provideShapeArrays () : iterable {
        yield [[1, 0, 0, 1, 0, 0, 0, 0], 4, [1, 0, 0, 1]];
        yield [[1, 0, 0, 1], 4, [1, 0, 0, 1]];
        yield [[1, 0, 0, 1], 3, [1, 0, 0]];

        yield [[1, 0, 0, 1], 0, []];
        yield [[], 0, []];
        yield [[], 4, [0, 0, 0, 0]];
    }

    /**
     * @dataProvider provideShapeArrays
     */
    public function testShapeCapacity (array $initial, int $length, array $final) : void {
        $bits = new BitsVector($initial);
        $bits->shapeCapacity($length);

        $this->assertEquals($final, $bits->toArray());
    }

    public function testCopyInteger() : void {
        $bits = BitsVector::new(8);
        $bits->copyInteger(5, 2, 3);

        $this->assertEquals([0, 0, 1, 0, 1, 0, 0, 0], $bits->toArray());
    }

    ///
    /// BaseVector overrides
    ///

    public function testSet () : void {
        $bits = BitsVector::new(4);
        $bits->set(2, 1);

        $this->assertEquals([0, 0, 1, 0], $bits->toArray());
    }

    public function testContains () : void {
        $bits = BitsVector::new(4);

        $this->assertFalse($bits->contains(1));
    }

    public function testPush () : void {
        $bits = BitsVector::new(4);
        $bits->push(1);

        $this->assertEquals([0, 0, 0, 0, 1], $bits->toArray());

    }

    public function testAppend () : void {
        $bits = BitsVector::new(4);

        $bits->append([1, 1]);
        $this->assertEquals([0, 0, 0, 0, 1, 1], $bits->toArray());
    }

    public function testUpdate () : void {
        $bits = BitsVector::new(4);

        $bits->update([1, 0, 1, 0]); // 0 already exists, we'll add ONE 1
        $this->assertEquals([0, 0, 0, 0, 1], $bits->toArray());
    }

    public function testOffsetSet () : void {
        $bits = BitsVector::new(4);
        $bits[2] = 1;

        $this->assertEquals([0, 0, 1, 0], $bits->toArray());
    }

    ///
    /// WithCollection trait
    ///

    public function testFirst () : void {
        $bits = BitsVector::new(4);
        $bits[2] = 1;

        $this->assertEquals(0, $bits->first());
    }

    public function testFirstWhenEmpty () : void {
        $bits = BitsVector::new(0);

        $this->expectException(OutOfRangeException::class);
        $bits->first();
    }

    public function testFirstOr () : void {
        $bits = BitsVector::new(4);
        $bits[2] = 1;

        $this->assertEquals(0, $bits->firstOr(2));
    }

    public function testFirstOrWhenEmpty () : void {
        $bits = BitsVector::new(0);

        $this->assertEquals(2, $bits->firstOr(2));
    }

}
