<?php

namespace Keruald\Yaml\Tests;

use Keruald\Yaml\Parser;

use PHPUnit\Framework\TestCase;

use InvalidArgumentException;

class ParserTest extends TestCase {

    private Parser $parser;

    protected function setUp () : void {
        $this->parser = new Parser;
    }

    public function testParse () {
        $this->assertEquals(666, $this->parser->parse("666"));
        $this->assertEquals("", $this->parser->parse(""));
    }

    public function testParseUnknownTag () {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Tag not found: foo");

        $this->parser->parse("!foo 4");
    }

    public function testParseFile () {
        $expected = [
            "example" => [
                "foo" => "bar",
                "skills" => [
                    "quux",
                    "baz",
                ],
            ],
        ];

        $actual = $this->parser->parseFile(__DIR__ . "/data/example.yaml");

        $this->assertEquals($expected, $actual);
    }

}
