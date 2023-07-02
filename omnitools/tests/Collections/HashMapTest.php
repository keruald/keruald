<?php

namespace Keruald\OmniTools\Tests\Collections;

use Keruald\OmniTools\Collections\BitsVector;
use Keruald\OmniTools\Collections\HashMap;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use InvalidArgumentException;
use IteratorAggregate;
use OutOfRangeException;
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

    public static function provideDeepArrays() : iterable {
        yield [self::MAP_CONTENT, self::MAP_CONTENT];

        yield [[], []];
        yield [null, []];

        $caps = new \stdClass;
        $caps->color = "red";
        $caps->logo = "HCKR";
        yield [$caps, [
            "color" => "red",
            "logo" => "HCKR",
        ]];

        $sizedCaps = clone $caps;
        $sizedCaps->size = new \stdClass;
        $sizedCaps->size->h = 8;
        $sizedCaps->size->r = 20;
        yield [$sizedCaps, [
            "color" => "red",
            "logo" => "HCKR",
            "size" => ["h" => 8, "r" => 20],
        ]];
    }

    #[DataProvider('provideDeepArrays')]
    public function testFrom($from, array $expected) : void {
        $map = HashMap::from($from);
        $this->assertEquals($expected, $map->toArray());
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

    public function testUnset() {
        $this->map->unset("The Culture");
        $this->assertFalse($this->map->contains("Iain Banks"));
    }

    public function testUnsetNotExistingKey() {
        $this->map->unset("Not existing");
        $this->assertEquals(4, $this->map->count());
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

    public function testIsEmpty () : void {
        $this->map->clear();

        $this->assertTrue($this->map->isEmpty());
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

    public function testMapKeysAndValues () : void {
        $callback = function ($civilization, $author) {
            return [$author[0], "$author, $civilization"];
        };

        $expected = [
            // Some sci-fi civilizations and author
            "I" => "Iain Banks, The Culture",
            "A" => "Ann Leckie, Radchaai Empire",
            "L" => "Lois McMaster Bujold, Barrayar",
            "U"=> "Ursula K. Le Guin, Hainish",
        ];

        $actual = $this->map->mapValuesAndKeys($callback)->toArray();
        $this->assertEquals($expected, $actual);
    }

    public function testMapKeysAndValuesForVectors () : void {
        $callback = function ($author) {
            return [$author[0], "author:" . $author];
        };

        $expected = [
            // Some sci-fi civilizations and author
            "I" => "author:Iain Banks",
            "A" => "author:Ann Leckie",
            "L" => "author:Lois McMaster Bujold",
            "U" => "author:Ursula K. Le Guin",
        ];

        $actual = $this->map->mapValuesAndKeys($callback)->toArray();
        $this->assertEquals($expected, $actual);
    }

    public function testMapKeysAndValuesWithCallbackWithoutArgument() : void {
        $this->expectException(InvalidArgumentException::class);

        $callback = function () {};
        $this->map->mapValuesAndKeys($callback);
    }

    public function testFlatMap(): void {
        $callback = function ($key, $value) {
            $items = explode(" ", $value);

            foreach ($items as $item) {
                yield $item => $key;
            }
        };

        $expected = [
            "Iain" => "The Culture",
            "Banks" => "The Culture",

            "Ann" => "Radchaai Empire",
            "Leckie" => "Radchaai Empire",

            "Lois" => "Barrayar",
            "McMaster" => "Barrayar",
            "Bujold" => "Barrayar",

            "Ursula"=> "Hainish",
            "K."=> "Hainish",
            "Le"=> "Hainish",
            "Guin"=> "Hainish",
        ];

        $actual = $this->map->flatMap($callback)->toArray();
        $this->assertEquals($expected, $actual);
    }

    public function testFlatMapForVectors() : void {
        $callback = function ($value) {
            $items = explode(" ", $value);

            foreach ($items as $item) {
                yield $item => $value;
            }
        };

        $expected = [
            "Iain" => "Iain Banks",
            "Banks" => "Iain Banks",

            "Ann" => "Ann Leckie",
            "Leckie" => "Ann Leckie",

            "Lois" => "Lois McMaster Bujold",
            "McMaster" => "Lois McMaster Bujold",
            "Bujold" => "Lois McMaster Bujold",

            "Ursula"=> "Ursula K. Le Guin",
            "K."=> "Ursula K. Le Guin",
            "Le"=> "Ursula K. Le Guin",
            "Guin"=> "Ursula K. Le Guin",
        ];

        $actual = $this->map->flatMap($callback)->toArray();
        $this->assertEquals($expected, $actual);
    }

    public function testFlatMapWithCallbackWithoutArgument() : void {
        $this->expectException(InvalidArgumentException::class);

        $callback = function () {};
        $this->map->flatMap($callback);
    }

    public function testMapToVector() : void {
        $expected = [
            "The Culture by Iain Banks",
            "Radchaai Empire by Ann Leckie",
            "Barrayar by Lois McMaster Bujold",
            "Hainish by Ursula K. Le Guin",
        ];

        $fn = fn($key, $value) => "$key by $value";

        $this->assertEquals($expected, $this->map->mapToVector($fn)->toArray());
    }

    public function testMapToVectorWithOnlyValueParameter() : void {
        $expected = [
            "Author: Iain Banks",
            "Author: Ann Leckie",
            "Author: Lois McMaster Bujold",
            "Author: Ursula K. Le Guin",
        ];

        $fn = fn($value) => "Author: $value";

        $this->assertEquals($expected, $this->map->mapToVector($fn)->toArray());
    }

    public function testMapToVectorWithoutParameter() : void {
        $this->expectException(InvalidArgumentException::class);

        $fn = fn() => "";

        $this->map->mapToVector($fn);
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

    ///
    /// ArrayAccess
    ///

    public function testOffsetExists () : void {
        $this->assertTrue(isset($this->map["The Culture"]));
        $this->assertFalse(isset($this->map["Not existing"]));
    }

    public function testOffsetSetWithoutOffset () : void {
        $this->expectException(InvalidArgumentException::class);
        $this->map[] = "Another Author";
    }

    public function testOffsetSet () : void {
        $this->map["The Culture"] = "Iain M. Banks";
        $this->assertEquals("Iain M. Banks", $this->map["The Culture"]);
    }

    public function testOffsetUnset () : void {
        unset($this->map["Barrayar"]);

        $expected = [
            "The Culture" => "Iain Banks",
            "Radchaai Empire" => "Ann Leckie",
            // "Barrayar" => "Lois McMaster Bujold",   UNSET ENTRY
            "Hainish" => "Ursula K. Le Guin",
        ];

        $this->assertEquals($expected, $this->map->toArray());
    }

    ///
    /// IteratorAggregate
    ///

    public function testGetIterator () : void {
        $this->assertEquals(self::MAP_CONTENT, iterator_to_array($this->map));
    }

    ///
    /// WithCollection trait
    ///

    public function testFirst () : void {
        $this->assertEquals("Iain Banks", $this->map->first());
    }

    public function testFirstWhenEmpty () : void {
        $map = new HashMap();

        $this->expectException(OutOfRangeException::class);
        $map->first();
    }

    public function testFirstOr () : void {
        $bits = BitsVector::new(4);
        $bits[2] = 1;

        $this->assertEquals("Iain Banks", $this->map->firstOr("Anonymous"));
    }

    public function testFirstOrWhenEmpty () : void {
        $map = new HashMap();

        $this->assertEquals("Anonymous", $map->firstOr("Anonymous"));
    }

}
