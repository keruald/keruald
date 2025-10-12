<?php

namespace Keruald\OmniTools\Tests\Reflection;

use Keruald\OmniTools\Reflection\Type;
use Keruald\OmniTools\Strings\Multibyte\OmniString;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TypeTest extends TestCase {

    public static function provideTypes () : iterable {
        // From gettype()
        yield ["foo", "string"];
        yield [42, "integer"];

        // From get_class()
        yield [new OmniString(), OmniString::class];
    }

    #[DataProvider("provideTypes")]
    function testGetTypeOf(mixed $value, string $expectedType) : void {
        $this->assertEquals($expectedType, Type::getTypeOf($value));
    }

}
