<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Tests\Strings\Multibyte;

use Keruald\OmniTools\Strings\Multibyte\OmniString;
use PHPUnit\Framework\TestCase;

use InvalidArgumentException;

class OmniStringTest extends TestCase {

    /**
     * @var OmniString
     */
    private $string;

    protected function setUp () {
        $this->string = new OmniString("foo");
    }

    public function testToString () : void {
        $this->assertEquals("foo", (string)$this->string);
        $this->assertEquals("foo", $this->string->__toString());
    }

    public function testPad () : void {
        $paddedString = $this->string->pad(9, '-=-', STR_PAD_BOTH);
        $this->assertEquals("-=-foo-=-", $paddedString);
    }

}
