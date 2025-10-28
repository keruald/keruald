<?php

namespace Keruald\OmniTools\Tests\OS;

use InvalidArgumentException;
use Keruald\OmniTools\DataTypes\Option\Some;
use Keruald\OmniTools\OS\Environment;
use PHPUnit\Framework\Attributes\DataProvider;
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

    public static function provideEnvironment () : iterable {
        yield ["foo", "bar"];
        yield ["bar", "lorem"];
        yield ["baz", "ipsum"];
    }

    public static function provideEnvironmentKeys () : iterable {
        foreach (self::provideEnvironment() as $kv) {
            yield [$kv[0]];
        }
    }

    #[DataProvider('provideEnvironmentKeys')]
    public function testHas (string $key) : void {
        self::assertTrue(Environment::has($key));
    }

    #[DataProvider('provideEnvironment')]
    public function testGet (string $key, string $value) : void {
        self::assertSame($value, Environment::get($key));
    }

    #[DataProvider('provideEnvironment')]
    public function testTryGet (string $key, string $value) : void {
        $actual = Environment::tryGet($key);

        $this->assertEquals(new Some($value), $actual);
    }

    public function testTryGetWhenKeyDoesNotExist () : void {
        $actual = Environment::tryGet("quux");

        $this->assertTrue($actual->isNone());
    }

    #[DataProvider('provideEnvironment')]
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
