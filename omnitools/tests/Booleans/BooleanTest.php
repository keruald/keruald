<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Tests\Booleans;

use Keruald\OmniTools\Booleans\Boolean;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class BooleanTest extends TestCase {

    ///
    /// Constructors
    ///

    public function testConstruct () {
        $this->assertTrue((new Boolean(true))->asBool());
        $this->assertFalse((new Boolean(false))->asBool());
    }

    public function testTrue () {
        $this->assertTrue(Boolean::true()->asBool());
    }

    public function testFalse () {
        $this->assertFalse(Boolean::false()->asBool());
    }

    ///
    /// Operations
    ///

    public static function provideAnd () : iterable {
        yield [true, true, true];
        yield [true, false, false];
        yield [false, true, false];
        yield [false, false, false];
    }

    #[DataProvider("provideAnd")]
    public function testAnd ($left, $right, $result) {
        $bool = new Boolean($left);

        $this->assertEquals($result, $bool->and($right)->asBool());
    }

    public static function provideOr () : iterable {
        yield [true, true, true];
        yield [true, false, true];
        yield [false, true, true];
        yield [false, false, false];
    }

    #[DataProvider("provideOr")]
    public function testOr ($left, $right, $result) {
        $bool = new Boolean($left);

        $this->assertEquals($result, $bool->or($right)->asBool());
    }

    public function testNot () {
        $this->assertFalse(Boolean::true()->not()->asBool());
        $this->assertTrue(Boolean::false()->not()->asBool());
    }

    public static function provideXor () : iterable {
        yield [true, true, false];
        yield [true, false, true];
        yield [false, true, true];
        yield [false, false, false];
    }

    #[DataProvider("provideXor")]
    public function testXor ($left, $right, $result) {
        $bool = new Boolean($left);

        $this->assertEquals($result, $bool->xor($right)->asBool());
    }

    public static function provideImplications () : iterable {
        yield [true, true, true];
        yield [true, false, false];
        yield [false, true, true];
        yield [false, false, true];
    }

    #[DataProvider("provideImplications")]
    public function testImplication ($left, $right, $result) {
        $bool = new Boolean($left);

        $this->assertEquals($result, $bool->implication($right)->asBool());
    }

    public static function provideEquivalence () : iterable {
        yield [true, true, true];
        yield [true, false, false];
        yield [false, true, false];
        yield [false, false, true];
    }

    #[DataProvider("provideEquivalence")]
    public function testEquivalence ($left, $right, $result) {
        $bool = new Boolean($left);

        $this->assertEquals($result, $bool->equivalence($right)->asBool());
    }

    ///
    /// Type convert
    ///

    public function testAsBool () {
        $this->assertTrue((new Boolean(true))->asBool());
        $this->assertFalse((new Boolean(false))->asBool());
    }

    public function testAsInteger () {
        $this->assertEquals(1, (new Boolean(true))->asInteger());
        $this->assertEquals(0, (new Boolean(false))->asInteger());
    }

    public function testAsString () {
        $this->assertEquals("true", (new Boolean(true))->asString());
        $this->assertEquals("false", (new Boolean(false))->asString());
    }

    public static function provideScalars () : iterable {
        yield [true, true];
        yield [Boolean::true(), true];
        yield [false, false];
        yield [Boolean::false(), false];
    }

    #[DataProvider("provideScalars")]
    public function testToScalar ($scalar, $expected) {
        $this->assertEquals($expected, Boolean::toScalar($scalar));
    }

}
