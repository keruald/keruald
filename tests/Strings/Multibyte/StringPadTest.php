<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Tests\Strings\Multibyte;

use Keruald\OmniTools\Strings\Multibyte\StringPad as Pad;
use PHPUnit\Framework\TestCase;

use InvalidArgumentException;

class StringPadTest extends TestCase {

    public function testSetPadTypeWithBogusValue () : void {
        $this->expectException(InvalidArgumentException::class);

        $pad = new Pad;
        $pad->setPadType(7);
    }

    public function testIsValidPadType () : void {
        $this->assertTrue(Pad::isValidPadType(STR_PAD_LEFT));
        $this->assertTrue(Pad::isValidPadType(STR_PAD_RIGHT));
        $this->assertTrue(Pad::isValidPadType(STR_PAD_BOTH));

        $this->assertFalse(Pad::isValidPadType(7));
    }

    public function testSetPadTypeWithBogusEncoding () : void {
        $this->expectException(InvalidArgumentException::class);

        $pad = new Pad;
        $pad->setEncoding("notexisting");
    }

    public function testSetLeftPad () : void {
        $pad = new Pad;
        $pad->setLeftPad();

        $this->assertEquals(STR_PAD_LEFT, $pad->getPadType());
    }

    public function testSetRightPad () : void {
        $pad = new Pad;
        $pad->setRightPad();

        $this->assertEquals(STR_PAD_RIGHT, $pad->getPadType());
    }

    public function testSetBothPad () : void {
        $pad = new Pad;
        $pad->setBothPad();

        $this->assertEquals(STR_PAD_BOTH, $pad->getPadType());
    }

}
