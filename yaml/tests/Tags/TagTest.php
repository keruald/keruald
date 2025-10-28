<?php

namespace Keruald\Yaml\Tests\Tags;

use Keruald\Yaml\Tags\Tag;
use PHPUnit\Framework\TestCase;

class TagTest extends TestCase {

    public static function provideTag () : Tag {
        return new class () extends Tag {
            public function getPrimaryTag () : string {
                return "tag:example.com,2000:test";
            }

            public function getPrivateTag () : string {
                return "test";
            }

            public function handle (mixed $data) : string {
                return "";
            }
        };
    }

    public function testRegister () {
        $handlers = [];

        $tag = self::provideTag();
        $tag->register($handlers);

        $this->assertArrayHasKey("tag:example.com,2000:test", $handlers);
        $this->assertArrayHasKey("test", $handlers);
    }

}
