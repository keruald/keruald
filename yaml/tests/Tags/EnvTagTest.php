<?php

namespace Keruald\Yaml\Tests\Tags;

use Keruald\Yaml\Parser;
use Keruald\Yaml\Tags\EnvTag;
use PHPUnit\Framework\TestCase;

class EnvTagTest extends TestCase {

    private EnvTag $tag;

    protected function setUp () : void {
        $this->tag = new EnvTag();

        $_ENV["ORBEON_DB_HOST"] = "localhost";
        $_ENV["ORBEON_DB_PORT"] = "5432";

        $_SERVER["ORBEON_DB_NAME"] = "orbeon_db";
    }

    public function testHandle () {
        $this->assertEquals("localhost", $this->tag->handle("ORBEON_DB_HOST"));
    }

    public function testHandleWithServerVariable () {
        $this->assertEquals("orbeon_db", $this->tag->handle("ORBEON_DB_NAME"));
    }

    public function testHandleWithMissingVariable () {
        $this->assertEquals("5432", $this->tag->handle("ORBEON_DB_PORT"));
    }

    ///
    /// Integration
    ///

    public function testFullParse () {
        $yamlContent = <<<YAML
database:
  host: !env ORBEON_DB_HOST
  port: !env ORBEON_DB_PORT
  name: !env ORBEON_DB_NAME
  pool_size: 10
YAML;

        $expected = [
            "database" => [
                "host" => "localhost",
                "port" => "5432",
                "name" => "orbeon_db",
                "pool_size" => 10,
            ],
        ];

        $parser = new Parser();
        $parser->withTagClass(EnvTag::class);
        $actual = $parser->parse($yamlContent);

        $this->assertEquals($expected, $actual);
    }

}
