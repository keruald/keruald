<?php

namespace Keruald\OmniTools\Tests\DataTypes\Option;

use Keruald\OmniTools\DataTypes\Option\None;
use Keruald\OmniTools\DataTypes\Option\Option;
use Keruald\OmniTools\DataTypes\Option\Some;

use PHPUnit\Framework\TestCase;

class OptionTest extends TestCase {

    public function testFrom () : void {
        $this->assertEquals(new Some(42), Option::from(42));
    }

    public function testFromWhenValueIsNull () : void {
        $actual = Option::from(null);

        $this->assertTrue($actual->isNone());
    }

}
