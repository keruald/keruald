<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Tests\Strings\Multibyte;

use Keruald\OmniTools\Strings\Multibyte\OmniString;
use PHPUnit\Framework\TestCase;

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

    public function testStartsWith () : void {
        $this->assertTrue($this->string->startsWith("fo"));
        $this->assertTrue($this->string->startsWith(""));
        $this->assertTrue($this->string->startsWith("foo"));

        $this->assertFalse($this->string->startsWith("Fo"));
        $this->assertFalse($this->string->startsWith("bar"));
    }

    public function testEndsWith () : void {
        $this->assertTrue($this->string->endsWith("oo"));
        $this->assertTrue($this->string->endsWith(""));
        $this->assertTrue($this->string->endsWith("foo"));

        $this->assertFalse($this->string->endsWith("oO"));
        $this->assertFalse($this->string->endsWith("bar"));
    }

}
