<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Tests\Identifiers;

use Keruald\OmniTools\Identifiers\UUID;
use Phpunit\Framework\TestCase;

class UUIDTest extends TestCase {

    public function testUUIDv4 () : void {
        $uuid = UUID::UUIDv4();

        $this->assertEquals(
            36, strlen($uuid),
            "UUID size must be 36 characters."
        );

        $re = "/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/";
        $this->assertRegExp($re, $uuid);
    }

    public function testUUIDv4WithoutHyphens () : void {
        $uuid = UUID::UUIDv4WithoutHyphens();

        $this->assertEquals(
            32, strlen($uuid),
            "UUID size must be 36 characters, and there are 4 hyphens, so here 32 characters are expected."
        );

        $re = "/[0-9a-f]/";
        $this->assertRegExp($re, $uuid);
    }

    public function testUUIDv4AreUnique () : void {
        $this->assertNotEquals(UUID::UUIDv4(), UUID::UUIDv4());
    }

}
