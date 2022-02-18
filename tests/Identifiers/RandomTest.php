<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Tests\Identifiers;

use Keruald\OmniTools\Identifiers\Random;
use Phpunit\Framework\TestCase;

class RandomTest extends TestCase {

    ///
    /// Tests
    ///

    public function testGenerateHexadecimalHash () : void {
            $hash = Random::generateHexHash();

            $this->assertEquals(
                32, strlen($hash),
                "$hash size must be 32 characters"
            );

            $this->assertMatchesRegularExpression("/[0-9a-f]{32}/", $hash);
    }

    public function testHexadecimalHashesAreUnique() : void {
        $this->assertNotEquals(
            Random::generateHexHash(),
            Random::generateHexHash()
        );
    }

    /**
     * @dataProvider provideRandomStringFormats
     */
    public function testRandomString($format, $re, $len) : void {
        $string = Random::generateString($format);

        $this->assertEquals($len, strlen($format));
        $this->assertMatchesRegularExpression($re, $string);
    }

    public function testGenerateIdentifier() : void {
        $identifier = Random::generateIdentifier(20);

        $this->assertEquals(27, strlen($identifier));
        $this->assertMatchesRegularExpression("/^[A-Z0-9\-_]*$/i", $identifier);
    }

    ///
    /// Data providers
    ///

    public function provideRandomStringFormats() : iterable {
        yield ["AAA111", "/^[A-Z]{3}[0-9]{3}$/", 6];
        yield ["AAA123", "/^[A-Z]{3}[0-9]{3}$/", 6];
        yield ["ABC123", "/^[A-Z]{3}[0-9]{3}$/", 6];
        yield ["", "/^$/", 0];
    }

}
