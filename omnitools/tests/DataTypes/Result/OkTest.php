<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Tests\DataTypes\Result;

use Exception;

use Keruald\OmniTools\DataTypes\Result\Ok;
use PHPUnit\Framework\TestCase;

class OkTest extends TestCase {

    public function setUp () : void {
        $this->v = new Ok;
        $this->v->setValue(42);
    }

    public function testIsOk () : void {
        $this->AssertTrue($this->v->isOk());
    }

    public function testIsError () : void {
        $this->assertFalse($this->v->isError());
    }

    public function testGetValue () : void {
        $this->assertEquals(42, $this->v->getValue());
    }

    public function testSetValue () : void {
        $this->v->setValue(666);
        $this->assertEquals(666, $this->v->getValue());
    }

    public function testSetValueWhenTypeIsMutated () : void {
        $this->expectException("InvalidArgumentException");
        $this->v->setValue("Another type");
    }

    public function testMap () : void {
        $callback = function ($n) {
            return $n * 2;
        };

        $mapped_v = $this->v->map($callback);

        $this->assertEquals(84, $mapped_v->getValue());
    }

    public function testMapErr () : void {
        $callback = function (Exception $ex) {
            return new Exception();
        };

        $mapped_v = $this->v->mapErr($callback);

        $this->assertEquals($mapped_v, $this->v);
    }

    public function testOr () : void {
        $actual = $this->v->or(new Ok(666));

        $this->assertTrue($actual->isOk());
        $this->assertEquals(42, $actual->getValue());
    }

    public function testOrElse () : void {
        $actual = $this->v->orElse(fn () => new Ok(666));

        $this->assertTrue($actual->isOk());
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
