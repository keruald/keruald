<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Collections;

use Keruald\OmniTools\Reflection\CallableElement;
use Keruald\OmniTools\Strings\Multibyte\OmniString;

use ArrayAccess;
use ArrayIterator;
use InvalidArgumentException;
use IteratorAggregate;
use Traversable;

class Vector extends BaseCollection implements ArrayAccess, IteratorAggregate {

    ///
    /// Properties
    ///

    private array $items;

    ///
    /// Constructors
    ///

    public function __construct (iterable $items = []) {
        if (is_array($items)) {
            $this->items = $items;
            return;
        }

        foreach ($items as $item) {
            $this->items[] = $item;
        }
    }

    public static function from (iterable $items) : static {
        return new self($items);
    }

    ///
    /// Specialized constructors
    ///

    /**
     * Constructs a new instance of a vector by exploding a string
     * according a specified delimiter.
     *
     * @param string $delimiter The substring to find for explosion
     * @param string $string The string to explode
     * @param int $limit If specified, the maximum count of vector elements
     * @return static
     */
    public static function explode (string $delimiter, string $string,
                                    int $limit = PHP_INT_MAX) : self {
        // There is some discussion to know if this method belongs
        // to Vector or OmniString.
        //
        // The advantage to keep it here is we can have constructs like:
        //     Vector::explode(",", "1,1,2,3,5,8,13")
        //         ->toIntegers()
        //          >map(function($n) { return $n * $n; })
        //         ->toArray();
        //
        // In this chaining, it is clear we manipulate Vector methods.

        return (new OmniString($string))
            ->explode($delimiter, $limit);
    }

    ///
    /// Interact with collection content at key level
    ///

    public function get (int $key) : mixed {
        if (!array_key_exists($key, $this->items)) {
            throw new InvalidArgumentException("Key not found.");
        }

        return $this->items[$key];
    }

    public function getOr (int $key, mixed $defaultValue) : mixed {
        return $this->items[$key] ?? $defaultValue;
    }

    public function set (int $key, mixed $value) : static {
        $this->items[$key] = $value;

        return $this;
    }

    public function unset (int $key) : static {
        unset($this->items[$key]);

        return $this;
    }

    public function contains (mixed $value) : bool {
        return in_array($value, $this->items);
    }

    ///
    /// Interact with collection content at collection level
    ///

    public function count () : int {
        return count($this->items);
    }

    public function isEmpty () : bool {
        return $this->count() === 0;
    }

    public function clear () : self {
        $this->items = [];

        return $this;
    }

    public function push (mixed $item) : self {
        $this->items[] = $item;

        return $this;
    }

    /**
     * Append all elements of the specified iterable
     * to the current vector.
     *
     * If a value already exists, the value is still added
     * as a duplicate.
     *
     * @see update() when you need to only add unique values.
     */
    public function append (iterable $iterable) : self {
        foreach ($iterable as $value) {
            $this->items[] = $value;
        }

        return $this;
    }

    /**
     * Append all elements of the specified iterable
     * to the current vector.
     *
     * If a value already exists, it is skipped.
     *
     * @see append() when you need to always add everything.
     */
    public function update (iterable $iterable) : self {
        foreach ($iterable as $value) {
            if (!$this->contains($value)) {
                $this->items[] = $value;
            }
        }

        return $this;
    }

    /**
     * Gets a copy of the internal vector.
     *
     * Scalar values (int, strings) are cloned.
     * Objects are references to a specific objet, not a clone.
     *
     * @return array
     */
    public function toArray () : array {
        return $this->items;
    }

    ///
    /// HOF :: generic
    ///

    public function map (callable $callable) : self {
        return new self(array_map($callable, $this->items));
    }

    public function filter (callable $callable) : self {
        $argc = (new CallableElement($callable))->countArguments();

        if ($argc === 0) {
            throw new InvalidArgumentException(
                "Callback should have at least one argument"
            );
        }

        $mode = (int)($argc > 1);
        return new self(array_filter($this->items, $callable, $mode));
    }

    public function mapKeys (callable $callable) : self {
        $mappedVector = [];
        foreach ($this->items as $key => $value) {
            $mappedVector[$callable($key)] = $value;
        }

        return new self($mappedVector);
    }

    public function flatMap (callable $callable) : self {
        $argc = (new CallableElement($callable))->countArguments();

        $newMap = new self;
        foreach ($this->items as $key => $value) {
            $toAdd = match($argc) {
                0 => throw new InvalidArgumentException(self::CB_ZERO_ARG),
                1 => $callable($value),
                default => $callable($key, $value),
            };
            $newMap->append($toAdd);
        }

        return $newMap;
    }

    public function filterKeys (callable $callable) : self {
        return new self(
            array_filter($this->items, $callable, ARRAY_FILTER_USE_KEY)
        );
    }

    ///
    /// HOF :: specialized
    ///

    public function toIntegers () : self {
        array_walk($this->items, ArrayUtilities::toIntegerCallback());

        return $this;
    }

    public function implode(string $delimiter) : OmniString {
        return new OmniString(implode($delimiter, $this->items));
    }

    ///
    /// ArrayAccess
    /// Interface to provide accessing objects as arrays.
    ///

    private static function ensureOffsetIsInteger (mixed $offset) {
        if (is_int($offset)) {
            return;
        }

        throw new InvalidArgumentException(
            "Offset of a vector must be an integer."
        );
    }

    public function offsetExists (mixed $offset) : bool {
        self::ensureOffsetIsInteger($offset);

        return array_key_exists($offset, $this->items);
    }

    public function offsetGet (mixed $offset) : mixed {
        self::ensureOffsetIsInteger($offset);

        return $this->get($offset);
    }

    public function offsetSet (mixed $offset, mixed $value) : void {
        if ($offset === null) {
            $this->push($value);
            return;
        }

        self::ensureOffsetIsInteger($offset);

        $this->set($offset, $value);
    }

    public function offsetUnset (mixed $offset) : void {
        self::ensureOffsetIsInteger($offset);

        $this->unset($offset);
    }

    ///
    /// IteratorAggregate
    ///

    public function getIterator () : Traversable {
        return new ArrayIterator($this->items);
    }
}
