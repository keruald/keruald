<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Tests\Collections;

use Keruald\OmniTools\Collections\WeightedValue;
use PHPUnit\Framework\TestCase;

class WeightedValueTest extends TestCase {

    /**
     * @var WeightedValue
     */
    private $lowValue;

    /**
     * @var WeightedValue
     */
    private $highValue;

    ///
    /// Fixtures
    ///

    protected function setUp () : void {
        $this->lowValue = new WeightedValue("LOW", 0.1);
        $this->highValue = new WeightedValue("HIGH");
    }

    ///
    /// Tests
    ///

    public function testGetWeight () : void {
        $this->assertSame(0.1, $this->lowValue->getWeight());
        $this->assertSame(
            WeightedValue::DEFAULT_WEIGHT,
            $this->highValue->getWeight()
        );
    }

    public function testSetWeight () : void {
        $this->lowValue->setWeight(0.2);
        $this->assertSame(0.2, $this->lowValue->getWeight());
    }

    public function testGetValue () : void {
        $this->assertEquals("LOW", $this->lowValue->getValue());
        $this->assertEquals("HIGH", $this->highValue->getValue());
    }

    public function testSetValue () : void {
        $this->lowValue->setValue("FOO");
        $this->assertEquals("FOO", $this->lowValue->getValue());
    }

    public function testCompareTo () : void {
        $this->assertEquals(
            0,
            $this->lowValue->compareTo($this->lowValue)
        );

        $this->assertEquals(
            -1,
            $this->lowValue->compareTo($this->highValue)
        );

        $this->assertEquals(
            1,
            $this->highValue->compareTo($this->lowValue)
        );
    }

    public function testCompareToWithApplesAndPears () : void {
        $this->expectException("TypeError");
        $this->highValue->compareTo(new \stdClass);
    }

    /**
     * @dataProvider provideExpressionsToParse
     */
    public function testParse ($expression, $expectedValue, $expectedWeight) : void {
        $value = WeightedValue::Parse($expression);

        $this->assertEquals($expectedValue, $value->getValue());
        $this->assertEquals($expectedWeight, $value->getWeight());
    }

    ///
    /// Data providers
    ///

    public function provideExpressionsToParse () : iterable {
        yield ["", "", 1.0];
        yield ["de", "de", 1.0];
        yield ["de;q=1.0", "de", 1.0];
        yield ["de;q=0.7", "de", 0.7];
        yield [";;q=0.7", ";", 0.7];
    }

}
