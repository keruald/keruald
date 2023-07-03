<?php

namespace Keruald\OmniTools\Tests\Reflection;

use InvalidArgumentException;
use Keruald\OmniTools\Reflection\CallableElement;

use PHPUnit\Framework\TestCase;

class CallableElementTest extends TestCase {

    private CallableElement $closure;

    protected function setUp () : void {
        $fn = fn(int $a, int $b) : int => $a + $b;
        $this->closure = new CallableElement($fn);
    }

    public function testCountArguments () : void {
        $this->assertEquals(2, $this->closure->countArguments());
    }

    public function testCountArgumentsWhenThereIsNone () : void {
        $fn = fn() => null;
        $closure = new CallableElement($fn);

        $this->assertEquals(0, $closure->countArguments());
    }

    public function testHasReturnType () : void {
        $this->assertTrue($this->closure->hasReturnType("int"));
        $this->assertFalse($this->closure->hasReturnType("quux"));
    }

    public function testHasReturnTypeWhenThereIsNone () : void {
        // Closure without any explicit return type
        $fn = fn(int $a, int $b) => $a + $b;
        $closure = new CallableElement($fn);

        $this->assertFalse($closure->hasReturnType("int"));
    }

    public function testGetReturnType () : void {
        $this->assertEquals("int", $this->closure->getReturnType());
    }

    public function testGetReturnTypeWhenThereIsNone () : void {
        // Closure without any explicit return type
        $fn = fn(int $a, int $b) => $a + $b;
        $closure = new CallableElement($fn);

        $this->expectException(InvalidArgumentException::class);
        $closure->getReturnType();
    }

}
