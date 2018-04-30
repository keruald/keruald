<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Tests\Collections;

use Keruald\OmniTools\Collections\WeightedValue;
use Keruald\OmniTools\Collections\WeightedList;
use PHPUnit\Framework\TestCase;

class WeightedListTest extends TestCase {

    /**
     * @var WeightedList
     */
    private $list;

    ///
    /// Fixtures
    ///

    protected function setUp () : void {
        $this->list = new WeightedList;
        $this->list->add("LOW", 0.1);
        $this->list->add("HIGH", 4);
        $this->list->add("AVERAGE");
    }

    ///
    /// Tests
    ///

    public function testAdd () : void {
        $count = count($this->list);

        $this->list->add("ANOTHER");

        $this->assertEquals($count + 1, count($this->list));
    }

    public function testClear () : void {
        $this->list->clear();
        $this->assertEquals(0, count($this->list));
    }

    public function testGetHeaviest () : void {
        $this->assertEquals(4, $this->list->getHeaviest()->getWeight());
    }

    public function testToSortedArray () : void {
        $array = $this->list->toSortedArray();

        $this->assertEquals(3, count($array));
        $this->assertEquals(["HIGH", "AVERAGE", "LOW"], $array);
    }

    public function testToSortedArrayWithDuplicateValues () : void {
        $this->list->add("AVERAGE");
        $array = $this->list->toSortedArray();

        $this->assertEquals(4, count($array));
        $this->assertEquals(["HIGH", "AVERAGE", "AVERAGE", "LOW"], $array);
    }

    public function testGetIterator () : void {
        $count = 0;

        foreach ($this->list as $item) {
            $this->assertInstanceOf(WeightedValue::class, $item);
            $count++;
        }

        $this->assertEquals(3, $count);
    }

    public function testCount() : void {
        $this->assertEquals(3, $this->list->count());
    }

}
