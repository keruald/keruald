<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Tests\Collections;

use Keruald\OmniTools\Collections\TraversableUtilities;
use PHPUnit\Framework\TestCase;

use Countable;

class TraversableUtilitiesTest extends TestCase {

    /**
     * @dataProvider provideCountables
     */
    public function testCount ($expectedCount, $countable) {
        $this->assertEquals(
            $expectedCount, TraversableUtilities::count($countable)
        );
    }

    /**
     * @dataProvider provideNotCountables
     */
    public function testCountWithNotCountables ($notCountable) {
        $this->expectException("TypeError");
        TraversableUtilities::count($notCountable);
    }

    ///
    /// Data providers
    ///

    public function provideCountables () : iterable {
        yield [0, null];
        yield [0, false];
        yield [0, []];
        yield [3, ["a", "b", "c"]];
        yield [42, new class implements Countable {
                public function count () : int {
                    return 42;
                }
            }
        ];
    }

    public function provideNotCountables () : iterable {
        yield [true];
        yield [new \stdClass];
        yield [0];
        yield [""];
        yield ["abc"];
    }

}
