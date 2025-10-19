<?php

namespace Keruald\OmniTools\Tests\Events;

use Keruald\OmniTools\Events\Propagation;

use PHPUnit\Framework\TestCase;

use BadFunctionCallException;
use Exception;
use LogicException;
use RuntimeException;

class PropagationTest extends TestCase {

    private int $counter;
    private iterable $callbacks;

    protected function setUp () : void {
        $this->counter = 0;

        $this->callbacks = [
            function (int $a, int $b) {
                $this->counter++;
            },

            function (int $a, int $b) {
                $this->counter++;
            },
        ];
    }

    public function testCallWithoutAnyCallback () {
        $this->expectNotToPerformAssertions();

        Propagation::call([]);
    }

    public function testCall () {
        Propagation::call($this->callbacks, [3, 4]);

        $this->assertEquals(2, $this->counter);
    }

    public function testCallOrThrowWithCallbacks () {
        Propagation::callOrThrow($this->callbacks, [3, 4]);

        $this->assertEquals(2, $this->counter);
    }

    public function testCallOrThrowWithoutAnyCallback () {
        $this->expectException(RuntimeException::class);
        Propagation::callOrThrow([], [3, 4]);
    }

    public function testCallOrThrowWithCustomException () {
        $this->expectException(LogicException::class);
        Propagation::callOrThrow([], [3, 4], new LogicException);
    }

    public function testCallWhenArgumentIsAnException () {
        // Dubious case with anonymous functions using strong types

        $arguments = [
            3,
            Exception::class,
        ];

        // Inner exception need to be
        $this->expectException(BadFunctionCallException::class);
        Propagation::call([null], $arguments);
    }
}
