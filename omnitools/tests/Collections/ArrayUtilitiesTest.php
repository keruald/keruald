<?php

namespace Keruald\OmniTools\Tests\Collections;

use Keruald\OmniTools\Collections\ArrayUtilities;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ArrayUtilitiesTest extends TestCase {

    #[DataProvider('provideIntegersArray')]
    public function testToIntegers ($expected, $toConvert) {
        $this->assertEquals($expected, ArrayUtilities::toIntegers($toConvert));
    }

    public static function provideIntegersArray () : iterable {
        yield [[1, 2, 3], ["1", "2", "3"]];

        yield [[1, 2, 3], [1, 2, 3]];
        yield [[], []];
    }
}
