<?php

namespace Keruald\OmniTools\Tests\Collections;

use Keruald\OmniTools\Collections\ArrayUtilities;

use PHPUnit\Framework\TestCase;

class ArrayUtilitiesTest extends TestCase {

    /**
     * @dataProvider provideIntegersArray
     */
    public function testToIntegers ($expected, $toConvert) {
        $this->assertEquals($expected, ArrayUtilities::toIntegers($toConvert));
    }

    public function provideIntegersArray () : iterable {
        yield [[1, 2, 3], ["1", "2", "3"]];

        yield [[1, 2, 3], [1, 2, 3]];
        yield [[], []];
    }
}
