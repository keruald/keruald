<?php

namespace Keruald\OmniTools\DateTime;

use Keruald\OmniTools\Collections\BitsVector;

use DateTimeInterface;
use InvalidArgumentException;

class UUIDv7TimeStamp {

    private BitsVector $bits;

    ///
    /// Constructors
    ///

    private function __construct (?BitsVector $bits = null) {
        $this->bits = $bits ?: BitsVector::new(48);
    }

    public static function fromUUIDv7 (string $uuid) : self {
        $uuidBits = BitsVector::fromDecoratedHexString($uuid);

        return new self($uuidBits->slice(0, 48));
    }

    public static function fromBits (BitsVector $bits) : self {
        if ($bits->count() !== 48) {
            throw new InvalidArgumentException("UUIDv7 timestamps must be 48 bits long, 32 for unixtime, 16 for milliseconds.");
        }

        return new self($bits);
    }

    public static function fromInteger (int $value) : self {
        $bits = self::generateBitsVector($value);

        return new self($bits);
    }

    public static function fromUnixTime (int $time, int $ms = 0) : self {
        $value = self::computeIntegerValue($time, $ms);
        return self::fromInteger($value);
    }

    public static function fromDateTime(DateTimeInterface $dateTime) : self {
        return self::fromUnixTime($dateTime->getTimestamp());
    }

    public static function now () : self {
        [$micro, $time] = explode(" ", microtime());
        return self::fromUnixTime($time, floor($micro * 1000));
    }

    ///
    /// Helper methods to build a timestamp
    ///

    private static function computeIntegerValue (int $time, int $ms) : int {
        return $time * 1000 + $ms;
    }

    private static function generateBitsVector (int $time): BitsVector {
        $timeBits = BitsVector::fromInteger($time);
        $len = $timeBits->count();

        if ($len == 48) {
            return $timeBits;
        }

        if ($len > 48) {
            trigger_error("Timestamp is truncated to the least significative 48 bits.", E_USER_WARNING);
            return $timeBits->slice($len - 48, 48);
        }

        return BitsVector::new(48)
            ->replace($timeBits, 48 - $len, 48);
    }

    ///
    /// Properties
    ///

    public function toBitsVector () : BitsVector {
        return $this->bits;
    }

}
