<?php

namespace Keruald\OmniTools\Tests\DataTypes\Option;

use InvalidArgumentException;

use Keruald\OmniTools\DataTypes\Option\None;
use Keruald\OmniTools\DataTypes\Option\Option;
use Keruald\OmniTools\DataTypes\Option\Some;

use PHPUnit\Framework\TestCase;

class NoneTest extends TestCase {
    private Option $v;

    public function setUp () : void {
        $this->v = new None;
    }

    public function testIsSome () : void {
        $this->assertFalse($this->v->isSome());
    }

    public function testIsNone () : void {
        $this->assertTrue($this->v->isNone());
    }

    public function testGetValue () : void {
        $this->expectException(InvalidArgumentException::class);
        $this->v->getValue();
    }

    public function testMap () : void {
        $callback = function ($n) {
            return $n * 2;
        };

        $mapped_v = $this->v->map($callback);

        $this->assertEquals($mapped_v, $this->v);
    }

    public function testOr () : void {
        $actual = $this->v->or(new Some(666));

        $this->assertTrue($actual->isSome());
        $this->assertEquals(666, $actual->getValue());
    }

    public function testOrElse () : void {
        $actual = $this->v->orElse(fn () => new Some(666));

        $this->assertTrue($actual->isSome());
        $this->assertEquals(666, $actual->getValue());
    }

    public function testGetValueOr () : void {
        $value = $this->v->getValueOr(666);

        $this->assertEquals(666, $value);
    }

    public function testGetValueOrElse () : void {
        $value = $this->v->getValueOrElse(fn () => 666);

        $this->assertEquals(666, $value);
    }
}
