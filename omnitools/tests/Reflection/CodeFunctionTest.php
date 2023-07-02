<?php

namespace Keruald\OmniTools\Tests\Reflection;

use Keruald\OmniTools\Reflection\CodeFunction;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use InvalidArgumentException;
use ReflectionFunction;
use ReflectionParameter;

class CodeFunctionTest extends TestCase {

    #[DataProvider('provideFunctionParameters')]
    public function testGetParameterType (ReflectionParameter $parameter, string $type) {
        $this->assertEquals($type, CodeFunction::getParameterType($parameter));
    }

    public function testGetParameterTypeWhenNoTypeIsDefined () {
        $this->expectException(InvalidArgumentException::class);

        $function = new ReflectionFunction("Keruald\OmniTools\Tests\Reflection\doSomething");
        $parameters = $function->getParameters();

        CodeFunction::getParameterType($parameters[0]);
    }

    ///
    /// Data provider
    ///

    public static function provideFunctionParameters () : iterable {
        // array_change_key_case(array $array, int $case = CASE_LOWER): array
        $function = new ReflectionFunction("array_change_key_case");
        $parameters = $function->getParameters();

        yield [$parameters[0], "array"];
        yield [$parameters[1], "int"];
    }
}

function doSomething ($mixed) : void {

}
