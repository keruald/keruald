<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Tests\Collections;

use Keruald\OmniTools\Collections\OmniArray;
use PHPUnit\Framework\TestCase;

class OmniArrayTest extends TestCase {

    public function testMap () : void {
        $actual = (new OmniArray([1, 2, 3, 4, 5]))
            ->map(function ($x) { return $x * $x; })
            ->toArray();

        $this->assertEquals([1, 4, 9, 16, 25], $actual);
    }

    public function testImplode() : void {
        $actual = (new OmniArray(["a", "b", "c"]))
            ->implode(".")
            ->__toString();

        $this->assertEquals("a.b.c", $actual);
    }

    public function testImplodeWithoutDelimiter() : void {
        $actual = (new OmniArray(["a", "b", "c"]))
            ->implode("")
            ->__toString();

        $this->assertEquals("abc", $actual);
    }

    public function testExplode() : void {
        $actual = OmniArray::explode(".", "a.b.c");

        $this->assertEquals(["a", "b", "c"], $actual->toArray());
    }

    public function testExplodeWithoutDelimiter() : void {
        $actual = OmniArray::explode("", "a.b.c");

        $this->assertEquals(["a.b.c"], $actual->toArray());
    }

}
