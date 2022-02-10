<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Collections;

use Keruald\OmniTools\Reflection\CallableElement;

use InvalidArgumentException;

/**
 * An associative array allowing the use of chained
 *
 *
 * This class can be used as a service container,
 * an application context, to store configuration.
 */
class HashMap implements BaseCollection, BaseMap {

    ///
    /// Properties
    ///

    private array $map;

    ///
    /// Constructor
    ///

    public function __construct (iterable $iterable = []) {
        if (is_array($iterable)) {
            $this->map = (array)$iterable;
            return;
        }

        foreach ($iterable as $key => $value) {
            $this->map[$key] = $value;
        }
    }

    public static function from (iterable $items) : static {
        return new self($items);
    }

    ///
    /// Interact with map content at key level
    ///

    public function get (mixed $key) : mixed {
        if (!array_key_exists($key, $this->map)) {
            throw new InvalidArgumentException("Key not found.");
        }

        return $this->map[$key];
    }

    public function getOr (mixed $key, mixed $defaultValue) : mixed {
        return $this->map[$key] ?? $defaultValue;
    }

    public function set (mixed $key, mixed $value) : static {
        $this->map[$key] = $value;

        return $this;
    }

    public function has (mixed $key) : bool {
        return array_key_exists($key, $this->map);
    }

    public function contains (mixed $value) : bool {
        return in_array($value, $this->map);
    }

    ///
    /// Interact with collection content at collection level
    ///

    public function count () : int {
        return count($this->map);
    }

    public function isEmpty () : bool {
        return $this->count() === 0;
    }

    public function clear () : self {
        $this->map = [];

        return $this;
    }

    /**
     * Merge the specified map with the current map.
     *
     * If a key already exists, the value already set is kept.
     *
     * @see update() when you need to update with the new value.
     */
    public function merge (iterable $iterable) : self {
        foreach ($iterable as $key => $value) {
            $this->map[$key] ??= $value;
        }

        return $this;
    }

    /**
     * Merge the specified map with the current bag.
     *
     * If a key already exists, the value is updated with the new one.
     *
     * @see merge() when you need to keep old value.
     */
    public function update (iterable $iterable) : self {
        foreach ($iterable as $key => $value) {
            $this->map[$key] = $value;
        }

        return $this;
    }

    /**
     * Gets a copy of the internal map.
     *
     * Scalar values (int, strings) are cloned.
     * Objects are references to a specific objet, not a clone.
     *
     * @return array<string, mixed>
     */
    public function toArray () : array {
        return $this->map;
    }

    ///
    /// HOF
    ///

    public function map (callable $callable) : self {
        return new self(array_map($callable, $this->map));
    }

    public function filter (callable $callable) : self {
        $argc = (new CallableElement($callable))->countArguments();
        if ($argc === 0) {
            throw new InvalidArgumentException(
                "Callback should have at least one argument"
            );
        }
        $mode = (int)($argc > 1);

        return new self(
            array_filter($this->map, $callable, $mode)
        );
    }

    public function mapKeys (callable $callable) : self {
        $mappedMap = [];
        foreach ($this->map as $key => $value) {
            $mappedMap[$callable($key)] = $value;
        }

        return new self($mappedMap);
    }

    public function filterKeys (callable $callable) : self {
        return new self(
            array_filter($this->map, $callable, ARRAY_FILTER_USE_KEY)
        );
    }

}
