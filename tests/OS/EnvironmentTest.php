<?php

namespace Keruald\OmniTools\Tests\OS;

use InvalidArgumentException;
use Keruald\OmniTools\OS\Environment;
use PHPUnit\Framework\TestCase;

class EnvironmentTest extends TestCase {

    protected function setUp () : void {
        // Keep in sync with provideEnvironment data provider

        $_ENV['foo'] = "bar";
        $_SERVER['foo'] = "baz";

        $_ENV['bar'] = "lorem";
        $_SERVER['baz'] = "ipsum";

        // And quux isn't defined.
    }

    public function provideEnvironment () : iterable {
        yield ["foo", "bar"];
        yield ["bar", "lorem"];
        yield ["baz", "ipsum"];
    }

    public function provideEnvironmentKeys () : iterable {
        foreach ($this->provideEnvironment() as $kv) {
            yield [$kv[0]];
        }
    }

    /**
     * @dataProvider provideEnvironmentKeys
     */
    public function testHas (string $key) : void {
        self::assertTrue(Environment::has($key));
    }

    /**
     * @dataProvider provideEnvironment
     */
    public function testGet (string $key, string $value) : void {
        self::assertSame($value, Environment::get($key));
    }

    /**
     * @dataProvider provideEnvironment
     */
    public function testGetOrWhenKeyExists (string $key, string $value) : void {
        self::assertSame($value, Environment::getOr($key, "default"));
    }

    public function testHasWhenKeyDoesNotExist () : void {
        self::assertFalse(Environment::has("quux"));
    }


    public function testGetWhenKeyDoesNotExist () : void {
        $this->expectException(InvalidArgumentException::class);
        Environment::get("quux");
    }

    public function testGetOrWhenKeyDoesNotExist () : void {
        self::assertSame("default", Environment::getOr("quux", "default"));
    }

}
