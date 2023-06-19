<?php

namespace Keruald\OmniTools\Tests\Reflection;

use Keruald\OmniTools\Collections\Vector;
use Keruald\OmniTools\Reflection\CodeVariable;
use PHPUnit\Framework\TestCase;

class CodeVariableTest extends TestCase {

    public function testHasTypeWithObject () {
        $object = new Vector;
        $variable = CodeVariable::from($object);

        $this->assertTrue($variable->hasType(Vector::class));
    }

    /**
     * @dataProvider provideScalarsAndTypes
     */
    public function testHasTypeWithScalar (mixed $scalar, string $type) {
        $variable =  CodeVariable::from($scalar);

        $this->assertTrue($variable->hasType($type));
    }

    /**
     * @dataProvider provideScalars
     */
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

    private function provideScalars () : iterable {
        yield [0];
        yield [""];
        yield [19];
        yield ["This is Sparta."];
        yield [true];
        yield [false];
        yield [null];
    }

    private function provideScalarsAndTypes () : iterable {
        yield [0, "integer"];
        yield ["", "string"];
        yield [19, "integer"];
        yield ["This is Sparta.", "string"];
        yield [true, "boolean"];
        yield [false, "boolean"];
        yield [null, "NULL"];
    }
}
