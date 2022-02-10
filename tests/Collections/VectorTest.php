<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Tests\Collections;

use Keruald\OmniTools\Collections\Vector;

use PHPUnit\Framework\TestCase;

use InvalidArgumentException;
use IteratorAggregate;
use Traversable;

class VectorTest extends TestCase {

    private Vector $vector;

    protected function setUp () : void {
        $this->vector = new Vector([1, 2, 3, 4, 5]);
    }

    public function testConstructorWithIterable () : void {
        $iterable = new class implements IteratorAggregate {
            public function getIterator () : Traversable {
                yield 42;
                yield 100;
            }
        };

        $vector = new Vector($iterable);
        $this->assertEquals([42, 100], $vector->toArray());
    }

    public function testFrom () : void {
        $this->assertEquals([42, 100], Vector::from([42, 100])->toArray());
    }

    public function testGet () : void {
        $vector = new Vector(["a", "b", "c"]);

        $this->assertEquals("b", $vector->get(1));
    }

    public function testGetOverflow () : void {
        $this->expectException(InvalidArgumentException::class);

        $this->vector->get(800);
    }

    public function testGetOr () : void {
        $vector = new Vector(["a", "b", "c"]);

        $this->assertEquals("X", $vector->getOr(800, "X"));
    }

    public function testSet () : void {
        $vector = new Vector(["a", "b", "c"]);
        $vector->set(1, "x"); // should replace "b"

        $this->assertEquals(["a", "x", "c"], $vector->toArray());
    }

    public function testContains () : void {
        $this->assertTrue($this->vector->contains(2));
        $this->assertFalse($this->vector->contains(666));
    }

    public function testCount () : void {
        $this->assertEquals(5, $this->vector->count());
        $this->assertEquals(0, (new Vector)->count());
    }

    public function testClear () : void {
        $this->vector->clear();

        $this->assertEquals(0, $this->vector->count());
    }

    public function testIsEmpty () : void {
        $this->vector->clear();

        $this->assertTrue($this->vector->isEmpty());
    }

    public function testPush () : void {
        $this->vector->push(6);

        $this->assertEquals([1, 2, 3, 4, 5, 6], $this->vector->toArray());
    }

    public function testAppend () : void {
        $this->vector->append([6, 7, 8]);

        $this->assertEquals([1, 2, 3, 4, 5, 6, 7 ,8], $this->vector->toArray());
    }

    public function testUpdate () : void {
        $this->vector->update([5, 5, 5, 6, 7, 8]); // 5 already exists

        $this->assertEquals([1, 2, 3, 4, 5, 6, 7 ,8], $this->vector->toArray());
    }

    public function testMap () : void {
        $actual = $this->vector
            ->map(function ($x) { return $x * $x; })
            ->toArray();

        $this->assertEquals([1, 4, 9, 16, 25], $actual);
    }

    public function testMapKeys () : void {
        $vector = new Vector(["foo", "bar", "quux", "xizzy"]);

        $filter = function ($key) {
            return 0; // Let's collapse our array
        };

        $actual = $vector->mapKeys($filter)->toArray();
        $this->assertEquals(["xizzy"], $actual);

    }

    public function testFilter () : void {
        $vector = new Vector(["foo", "bar", "quux", "xizzy"]);

        $filter = function ($item) {
            return strlen($item) === 3; // Let's keep 3-letters words
        };

        $actual = $vector->filter($filter)->toArray();
        $this->assertEquals(["foo", "bar"], $actual);
    }

    public function testFilterWithBadCallback () : void {
        $this->expectException(InvalidArgumentException::class);

        $badFilter = function () {};

        $this->vector->filter($badFilter);
    }

    public function testFilterKeys () : void {
        $filter = function ($key) {
            return $key % 2 === 0; // Let's keep even indices
        };

        $actual = $this->vector
            ->filterKeys($filter)
            ->toArray();

        $this->assertEquals([0, 2, 4], array_keys($actual));
    }

    public function testImplode() : void {
        $actual = (new Vector(["a", "b", "c"]))
            ->implode(".")
            ->__toString();

        $this->assertEquals("a.b.c", $actual);
    }

    public function testImplodeWithoutDelimiter() : void {
        $actual = (new Vector(["a", "b", "c"]))
            ->implode("")
            ->__toString();

        $this->assertEquals("abc", $actual);
    }

    public function testExplode() : void {
        $actual = Vector::explode(".", "a.b.c");

        $this->assertEquals(["a", "b", "c"], $actual->toArray());
    }

    public function testExplodeWithoutDelimiter() : void {
        $actual = Vector::explode("", "a.b.c");

        $this->assertEquals(["a.b.c"], $actual->toArray());
    }

    ///
    /// ArrayAccess
    ///

    public function testArrayAccessFailsWithStringKey () : void {
        $this->expectException(InvalidArgumentException::class);

        $this->vector["foo"];
    }

    public function testOffsetExists () : void {
        $this->assertTrue(isset($this->vector[0]));
        $this->assertFalse(isset($this->vector[8]));
    }

    public function testOffsetSetWithoutOffset () : void {
        $this->vector[] = 6;
        $this->assertEquals(6, $this->vector[5]);
    }

    public function testOffsetSet () : void {
        $this->vector[0] = 9;
        $this->assertEquals(9, $this->vector[0]);
    }

    public function testOffsetUnset () : void {
        unset($this->vector[2]);

        $expected = [
            0 => 1,
            1 => 2,
            // vector[2] has been unset
            3 => 4,
            4 => 5,
        ];

        $this->assertEquals($expected, $this->vector->toArray());
    }

}
