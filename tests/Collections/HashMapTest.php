<?php

namespace Keruald\OmniTools\Tests\Collections;

use Keruald\OmniTools\Collections\HashMap;

use PHPUnit\Framework\TestCase;

use InvalidArgumentException;
use IteratorAggregate;
use Traversable;

class HashMapTest extends TestCase {

    ///
    /// Test set up
    ///

    private HashMap $map;

    const MAP_CONTENT = [
        // Some sci-fi civilizations and author
        "The Culture" => "Iain Banks",
        "Radchaai Empire" => "Ann Leckie",
        "Barrayar" => "Lois McMaster Bujold",
        "Hainish" => "Ursula K. Le Guin",
    ];

    protected function setUp () : void {
        $this->map = new HashMap(self::MAP_CONTENT);
    }

    ///
    /// Constructors
    ///

    public function testConstructorWithArray () {
        $this->assertSame(self::MAP_CONTENT, $this->map->toArray());
    }

    public function testConstructorWithTraversable () {
        $expected = [
            "color" => "blue",
            "material" => "glass",
            "shape" => "sphere",
        ];

        $iterable = new class implements IteratorAggregate {
            function getIterator () : Traversable {
                yield "color" => "blue";
                yield "material" => "glass";
                yield "shape" => "sphere";
            }
        };

        $map = new HashMap($iterable);
        $this->assertSame($expected, $map->toArray());
    }

    public function testFrom () {
        $map = HashMap::from(self::MAP_CONTENT);
        $this->assertSame(self::MAP_CONTENT, $map->toArray());
    }

    ///
    /// Getters and setters
    ///

    public function testGet () {
        $this->assertSame("Iain Banks", $this->map->get("The Culture"));
    }

    public function testGetWhenKeyIsNotFound () {
        $this->expectException(InvalidArgumentException::class);

        $this->map->get("Quuxians");
    }

    public function testGetOr () {
        $actual = $this->map
            ->getOr("The Culture", "Another author");

        $this->assertSame("Iain Banks", $actual);
    }

    public function testGetOrWhenKeyIsNotFound () {
        $actual = $this->map
            ->getOr("Quuxians", "Another author");

        $this->assertSame("Another author", $actual);
    }

    public function testSetWithNewKey () {
        $this->map->set("Thélème", "François Rabelais");

        $this->assertSame("François Rabelais",
            $this->map->get("Thélème"));
    }

    public function testSetWithExistingKey () {
        $this->map->set("The Culture", "Iain M. Banks");

        $this->assertSame("Iain M. Banks",
            $this->map->get("The Culture"));
    }

    public function testHas () {
        $this->assertTrue($this->map->has("The Culture"));
        $this->assertFalse($this->map->has("Not existing key"));
    }

    public function testContains () {
        $this->assertTrue($this->map->contains("Iain Banks"));
        $this->assertFalse($this->map->contains("Not existing value"));
    }

    ///
    /// Collection method
    ///

    public function testCount () {
        $this->assertSame(4, $this->map->count());
    }

    public function testClear () {
        $this->map->clear();
        $this->assertSame(0, $this->map->count());
    }

    public function testMerge () {
        $iterable = [
            "The Culture" => "Iain M. Banks", // existing key
            "Thélème" => "François Rabelais", // new key
        ];

        $expected = [
            // The original map
            "The Culture" => "Iain Banks", // Old value should be kept
            "Radchaai Empire" => "Ann Leckie",
            "Barrayar" => "Lois McMaster Bujold",
            "Hainish" => "Ursula K. Le Guin",

            // The entries with a new key
            "Thélème" => "François Rabelais",
        ];

        $this->map->merge($iterable);
        $this->assertSame($expected, $this->map->toArray());
    }

    public function testUpdate () {
        $iterable = [
            "The Culture" => "Iain M. Banks", // existing key
            "Thélème" => "François Rabelais", // new key
        ];

        $expected = [
            // The original map
            "The Culture" => "Iain M. Banks", // Old value should be updated
            "Radchaai Empire" => "Ann Leckie",
            "Barrayar" => "Lois McMaster Bujold",
            "Hainish" => "Ursula K. Le Guin",

            // The entries with a new key
            "Thélème" => "François Rabelais",
        ];

        $this->map->update($iterable);
        $this->assertSame($expected, $this->map->toArray());
    }

    public function testToArray () {
        $this->assertEquals(self::MAP_CONTENT, $this->map->toArray());
    }

    ///
    /// High order functions
    ///

    public function testMap () {
        $callback = function ($value) {
            return "author='" . $value . "'";
        };

        $expected = [
            "The Culture" => "author='Iain Banks'",
            "Radchaai Empire" => "author='Ann Leckie'",
            "Barrayar" => "author='Lois McMaster Bujold'",
            "Hainish" => "author='Ursula K. Le Guin'",
        ];

        $actual = $this->map->map($callback)->toArray();
        $this->assertEquals($expected, $actual);
    }

    public function testMapKeys () {
        $callback = function ($key) {
            return "civ::" . $key;
        };

        $expected = [
            // Some sci-fi civilizations and author
            "civ::The Culture" => "Iain Banks",
            "civ::Radchaai Empire" => "Ann Leckie",
            "civ::Barrayar" => "Lois McMaster Bujold",
            "civ::Hainish" => "Ursula K. Le Guin",
        ];

        $actual = $this->map->mapKeys($callback)->toArray();
        $this->assertEquals($expected, $actual);
    }

    public function testFilter () {
        // Let's filter to keep names with 3 parts or more

        $callback = function ($value) : bool {
            return str_word_count($value) > 2;
        };

        $expected = [
            // Some sci-fi civilizations and author
            "Barrayar" => "Lois McMaster Bujold",
            "Hainish" => "Ursula K. Le Guin",
        ];

        $actual = $this->map->filter($callback)->toArray();
        $this->assertEquals($expected, $actual);
    }

    public function testFilterWithKeyValueCallback () {
        // Let's find civilization AND author with e inside

        $expected = [
            // Some sci-fi civilizations and author
            "Radchaai Empire" => "Ann Leckie",
        ];

        $callback = function ($key, $value) : bool {
            return str_contains($key, "e") && str_contains($value, "e");
        };

        $actual = $this->map->filter($callback)->toArray();
        $this->assertEquals($expected, $actual);
    }

    public function testFilterWithCallbackWithoutArgument() {
        $this->expectException(InvalidArgumentException::class);

        $callback = function () : bool { // No argument
            return true;
        };

        $this->map->filter($callback);
    }

    public function testFilterKeys () {
        // Let's filter to keep short civilization names

        $callback = function ($key) : bool {
            return str_word_count($key) == 1;
        };

        $expected = [
            // Some sci-fi civilizations and author
            "Barrayar" => "Lois McMaster Bujold",
            "Hainish" => "Ursula K. Le Guin",
        ];

        $actual = $this->map->filterKeys($callback)->toArray();
        $this->assertEquals($expected, $actual);
    }

}
