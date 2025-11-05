<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Tests\DataTypes\Result;

use DivisionByZeroError;
use Exception;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

use Keruald\OmniTools\DataTypes\Result\Err;
use Keruald\OmniTools\DataTypes\Result\Ok;

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

    public function testOr () : void {
        $actual = $this->v->or(new Ok(666));

        $this->assertTrue($actual->isOk());
        $this->assertEquals(666, $actual->getValue());
    }

    public function testOrElse () : void {
        $actual = $this->v->orElse(fn () => new Ok(666));

        $this->assertTrue($actual->isOk());
        $this->assertEquals(666, $actual->getValue());
    }

    public function testOrElseWithCallbackArgument () : void {
        $actual = $this->v->orElse(
            fn ($error) => new Err(new RuntimeException(get_class($error)))
        );

        $this->assertTrue($actual->isError());
        $this->assertInstanceOf(RuntimeException::class, $actual->getError());
        $this->assertEquals("DivisionByZeroError", $actual->getError()->getMessage());
    }

    public function testOrElseWithTooManyCallbackArgument () : void {
        $this->expectException(InvalidArgumentException::class);
        $this->v->orElse(fn ($error, $extraneous) => get_class($error));
    }

    public function testGetValueOr () : void {
        $value = $this->v->getValueOr(666);

        $this->assertEquals(666, $value);
    }

    public function testGetValueOrElse () : void {
        $value = $this->v->getValueOrElse(fn () => 666);

        $this->assertEquals(666, $value);
    }

    public function testGetValueOrElseWithCallbackArgument () : void {
        $actual = $this->v->getValueOrElse(fn ($error) => get_class($error));

        $this->assertEquals("DivisionByZeroError", $actual);
    }

    public function testGetValueOrElseWithTooManyCallbackArgument () : void {
        $this->expectException(InvalidArgumentException::class);
        $this->v->getValueOrElse(fn ($error, $extraneous) => get_class($error));
    }
}
