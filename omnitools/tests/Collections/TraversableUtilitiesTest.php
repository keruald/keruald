<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Tests\Collections;

use Keruald\OmniTools\Collections\TraversableUtilities;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use Traversable;

class TraversableUtilitiesTest extends TestCase {

    #[DataProvider('provideCountables')]
    public function testCount ($expectedCount, $countable) {
        $this->assertEquals(
            $expectedCount, TraversableUtilities::count($countable)
        );
    }

    #[DataProvider('provideNotCountables')]
    public function testCountWithNotCountables ($notCountable) {
        $this->expectException("TypeError");
        TraversableUtilities::count($notCountable);
    }

    #[DataProvider('provideIterableAndFirst')]
    public function testIsFirst($expected, $iterable) {
        $this->assertEquals($expected, TraversableUtilities::first($iterable));
    }

    public function testIsFirstWithEmptyCollection() {
        $this->expectException(InvalidArgumentException::class);

        TraversableUtilities::first([]);
    }

    #[DataProvider('provideIterableAndFirst')]
    public function testIsFirstOr($expected, $iterable) {
        $actual = TraversableUtilities::firstOr($iterable, 666);
        $this->assertEquals($expected, $actual);
    }

    public function testIsFirstOrWithEmptyCollection() {
        $actual = TraversableUtilities::firstOr([], 666);
        $this->assertEquals(666, $actual);
    }

    ///
    /// Data providers
    ///

    public static function provideCountables () : iterable {
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

    public static function provideNotCountables () : iterable {
        yield [true];
        yield [new \stdClass];
        yield [0];
        yield [""];
        yield ["abc"];
    }

    public static function provideIterableAndFirst() : iterable {
        yield ["a", ["a", "b", "c"]];

        yield ["apple", ["fruit" => "apple", "vegetable" => "leeks"]];

        yield [42, new class implements IteratorAggregate {
            public function getIterator () : Traversable {
                yield 42;
                yield 100;
            }
        }];
    }

}
