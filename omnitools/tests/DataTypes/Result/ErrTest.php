<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Tests\DataTypes\Result;

use DivisionByZeroError;
use Exception;
use Throwable;

use Keruald\OmniTools\DataTypes\Result\Err;
use PHPUnit\Framework\TestCase;

class ErrTest extends TestCase {
    public function setUp () : void {
        $this->v = new Err;
        $this->v->setError(new DivisionByZeroError());
    }

    public function testIsOk () : void {
        $this->AssertFalse($this->v->isOk());
    }

    public function testIsError () : void {
        $this->assertTrue($this->v->isError());
    }

    public function testGetValue () : void {
        $this->expectException("InvalidArgumentException");
        $this->v->getValue();
    }

    public function testMap () : void {
        $callback = function ($n) {
            return $n * 2;
        };

        $mapped_v = $this->v->map($callback);

        $this->assertEquals($mapped_v, $this->v);
    }

    public function testMapErr () : void {
        $callback = function (Throwable $ex) {
            return new Exception();
        };

        $mapped_v = $this->v->mapErr($callback);
        $this->assertInstanceOf(Exception::class, $mapped_v->getError());
    }

    public function testOrElse () : void {
        $value = $this->v->orElse(666);

        $this->assertEquals(666, $value);
    }
}
