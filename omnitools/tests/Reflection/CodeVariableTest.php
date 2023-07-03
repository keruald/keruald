<?php

namespace Keruald\OmniTools\Tests\Reflection;

use Keruald\OmniTools\Collections\Vector;
use Keruald\OmniTools\Reflection\CodeVariable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CodeVariableTest extends TestCase {

    public function testHasTypeWithObject () {
        $object = new Vector;
        $variable = CodeVariable::from($object);

        $this->assertTrue($variable->hasType(Vector::class));
    }

    #[DataProvider('provideScalarsAndTypes')]
    public function testHasTypeWithScalar (mixed $scalar, string $type) {
        $variable =  CodeVariable::from($scalar);

        $this->assertTrue($variable->hasType($type));
    }

    public function testGetTypeWithObject () {
        $object = new Vector;
        $variable = CodeVariable::from($object);

        $this->assertEquals(Vector::class, $variable->getType());
    }

    #[DataProvider('provideScalarsAndReturnedTypes')]
    public function testGetTypeWithScalar (mixed $scalar, string $expected) {
        $variable =  CodeVariable::from($scalar);

        $this->assertEquals($expected, $variable->getType());
    }

    #[DataProvider('provideScalars')]
    public function testFromWithScalar (mixed $scalar) {
        $variable = CodeVariable::from($scalar);

        $this->assertInstanceOf(CodeVariable::class, $variable);
    }

    public function testFromWithObject () {
        $object = new Vector;
        $variable = CodeVariable::from($object);

        $this->assertInstanceOf(CodeVariable::class, $variable);
    }

    ///
    /// Data providers
    ///

    public static function provideScalars () : iterable {
        yield [0];
        yield [""];
        yield [19];
        yield ["This is Sparta."];
        yield [true];
        yield [false];
        yield [null];
    }

    public static function provideScalarsAndTypes () : iterable {
        yield [0, "integer"];
        yield ["", "string"];
        yield [19, "integer"];
        yield ["This is Sparta.", "string"];
        yield [true, "boolean"];
        yield [false, "boolean"];
        yield [null, "NULL"];
    }

    public static function provideScalarsAndReturnedTypes () : iterable {
        yield [0, "int"];
        yield ["", "string"];
        yield [19, "int"];
        yield ["This is Sparta.", "string"];
        yield [true, "bool"];
        yield [false, "bool"];
        yield [null, "NULL"];
    }
}
