<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Collections;

use Exception;
use InvalidArgumentException;

class BitsVector extends BaseVector {

    ///
    /// Constructors
    ///

    public static function new (int $capacity) : self {
        return match (true) {
            $capacity === 0 => new self([]),
            $capacity < 0 => throw new InvalidArgumentException("Capacity must be a positive number"),
            default => new self(array_fill(0, $capacity, 0)),
        };
    }

    public static function fromInteger (int $n) : self {
        return self::fromBinaryString(decbin($n));
    }

    public static function fromBinaryString (string $bits) : self {
        $vector = ArrayUtilities::toIntegers(str_split($bits));
        return new self($vector);
    }

    public static function fromHexString (string $number) : self {
        $bits = array_map(
            fn($n) => str_pad(decbin(sscanf(implode($n), "%x")[0]), 8, "0", STR_PAD_LEFT),
            array_chunk(str_split($number), 2)
        );

        return self::fromBinaryString(implode($bits));
    }

    public static function fromDecoratedHexString (string $expression) : self {
        $number = preg_replace("/[^a-fA-F0-9]/", "", $expression);
        return self::fromHexString($number);
    }

    public static function fromString (string $string) : self {
        return BitsVector::fromHexString(bin2hex($string));
    }

    /**
     * @throws Exception if an appropriate source of randomness cannot be found.
     */
    public static function random (int $length) : self {
        if ($length < 0) {
            throw new InvalidArgumentException("The length must be a positive value");
        }

        if ($length === 0) {
            return new self([]);
        }

        $randomBytes = random_bytes((int)ceil($length / 8));

        return BitsVector::fromString($randomBytes)->truncate($length);
    }

    ///
    /// Specialized methods to work with bits vector
    ///

    public function toBinaryString () : string {
        return implode($this->items);
    }

    public function toInteger () : int {
        if ($this->count() > 63) {
            throw new InvalidArgumentException("PHP doesn't allow representing integers greater than 64 bits.");
        }

        return bindec($this->toBinaryString());
    }

    private static function bitsToHex (array $bits) : string {
        $number = implode("", $bits);
        return base_convert($number, 2, 16);
    }

    public function toHexString () : string {
        $expectedLen = (int)ceil($this->count() / 4);

        return $this
            ->chunk(4)
            ->map(fn($chunk) => self::bitsToHex($chunk))
            ->implode("")
            ->pad($expectedLen, "0", STR_PAD_RIGHT)
            ;
    }

    public function toBytesArray () : array {
        if ($this->count() % 8 !== 0) {
            throw new InvalidArgumentException("This vector can't be represented in bytes: the bits count is not a multiple of 8.");
        }

        return $this
            ->chunk(8)
            ->map(fn ($chunk) => bindec(implode($chunk)))
            ->toArray();
    }

    /**
     * Pad the vector with 0 at the leftmost position ("zerofill")
     *
     * @param int $length The expected length of the vector
     */
    public function pad (int $length) : self {
        $currentCount = $this->count();

        if ($currentCount >= $length) {
            return $this;
        }

        $bits = array_fill(0, $length - $currentCount, 0);
        array_push($bits, ...$this->items);

        $this->items = $bits;
        return $this;
    }

    public function truncate (int $length) : self {
        if ($this->count() <= $length) {
            return $this;
        }

        $bits = array_slice($this->items, 0, $length);

        $this->items = $bits;
        return $this;
    }

    /**
     * Truncate or pad the array as needed to ensure a specified length.
     */
    public function shapeCapacity (int $length) : self {
        $currentCount = $this->count();

        if ($currentCount == $length) {
            return $this;
        }

        if ($currentCount < $length) {
            return $this->pad($length);
        }

        return $this->truncate($length);
    }

    /**
     * Copy the bits of the specified integer to the bits vector
     * at the specified offset.
     *
     * @param int $integer The integer to copy bits from
     * @param int $offset  The position in the vector where to start to copy
     * @param int $length  The length the bits of the integer occupy in the vector
     */
    public function copyInteger (int $integer, int $offset, int $length) : self {
        $toInsert = self::fromInteger($integer)
                        ->shapeCapacity($length);

        return $this->replace($toInsert, $offset, $length);
    }

    ///
    /// Ensure value is always 0 or 1 bit
    ///

    private static function assertValueIsBit (mixed $value) : void {
        if ($value !== 0 && $value !== 1) {
            throw new InvalidArgumentException("Only 0 and 1 are accepted as bit value.");
        }
    }

    private static function assertValueIsBitsIterable (iterable $bits) {
        foreach ($bits as $bit) {
            self::assertValueIsBit($bit);
        }
    }

    ///
    /// Override of BaseVector
    /// Ensure value is always 0 or 1 bit
    ///
    /// This section is the cost to pay for not having generics
    ///

    public function __construct (iterable $bits = []) {
        self::assertValueIsBitsIterable($bits);
        parent::__construct($bits);
    }

    public function set (int $key, mixed $value) : static {
        self::assertValueIsBit($value);
        return parent::set($key, $value);
    }
    
    public function contains (mixed $value) : bool {
        self::assertValueIsBit($value);
        return parent::contains($value);
    }

    public function push (mixed $item) : self {
        self::assertValueIsBit($item);
        return parent::push($item);
    }

    public function append (iterable $iterable) : self {
        self::assertValueIsBitsIterable($iterable);
        return parent::append($iterable);
    }

    public function update (iterable $iterable) : self {
        self::assertValueIsBitsIterable($iterable);
        return parent::update($iterable);
    }

    public function offsetSet (mixed $offset, mixed $value) : void {
        self::assertValueIsBit($value);
        parent::offsetSet($offset, $value);
    }

}
