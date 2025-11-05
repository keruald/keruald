<?php

namespace Keruald\OmniTools\Tests\DataTypes\Option;

use InvalidArgumentException;

use Keruald\OmniTools\DataTypes\Option\Some;
use Keruald\OmniTools\DataTypes\Option\Option;

use PHPUnit\Framework\TestCase;

class SomeTest extends TestCase {
    private Option $v;

    public function setUp () : void {
        $this->v = new Some;
        $this->v->setValue(42);
    }

    public function testIsSome () : void {
        $this->assertTrue($this->v->isSome());
    }

    public function testIsNone () : void {
        $this->assertFalse($this->v->isNone());
    }

    public function testGetValue () : void {
        $this->assertEquals(42, $this->v->getValue());
    }

    public function testSetValue () : void {
        $this->v->setValue(666);
        $this->assertEquals(666, $this->v->getValue());
    }

    public function testSetValueWhenTypeIsMutated () : void {
        $this->expectException(InvalidArgumentException::class);
        $this->v->setValue("Another type");
    }

    public function testMap () : void {
        $callback = function ($n) {
            return $n * 2;
        };

        $mapped_v = $this->v->map($callback);

        $this->assertEquals(84, $mapped_v->getValue());
    }

    public function testOr () : void {
        $actual = $this->v->or(new Some(666));

        $this->assertTrue($actual->isSome());
        $this->assertEquals(42, $actual->getValue());
    }

    public function testOrElse () : void {
        $actual = $this->v->orElse(fn () => new Some(666));

        $this->assertTrue($actual->isSome());
        $this->assertEquals(42, $actual->getValue());
    }

    public function testGetValueOr () : void {
        $value = $this->v->getValueOr(666);

        $this->assertEquals(42, $value);
    }

    public function testGetValueOrElse () : void {
        $value = $this->v->getValueOrElse(fn () => 666);

        $this->assertEquals(42, $value);
    }
}
