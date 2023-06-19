<?php

namespace Keruald\OmniTools\Reflection;

use Keruald\OmniTools\Collections\Vector;
use PHPUnit\Framework\TestCase;

use ReflectionMethod;

class CodeMethodTest extends TestCase {

    public function testGetArgumentsType () {
        // public function replace(iterable $iterable, int $offset = 0, int $len = 0) : self
        $method = new ReflectionMethod(new Vector, "replace");
        $method = CodeMethod::fromReflectionMethod($method);

        $expected = Vector::from([
            "iterable",
            "int",
            "int",
        ]);
        $actual = $method->getArgumentsType();

        $this->assertEquals($expected, $actual);
    }

    public function testFromReflectionMethod () {
        $method = new ReflectionMethod(new Vector, "replace");
        $method = CodeMethod::fromReflectionMethod($method);

        $this->assertInstanceOf(CodeMethod::class, $method);
    }

}
