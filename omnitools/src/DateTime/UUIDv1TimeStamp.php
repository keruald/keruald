<?php

namespace Keruald\OmniTools\DateTime;

use Keruald\OmniTools\Collections\BitsVector;

use DateTimeInterface;
use InvalidArgumentException;

class UUIDv1TimeStamp {

    private BitsVector $bits;

    ///
    /// Constants
    ///

    const OFFSET_BETWEEN_GREGORIAN_AND_UNIX_EPOCH = 0x01B21DD213814000;

    private function __construct (?BitsVector $bits = null) {
        $this->bits = $bits ?: BitsVector::new(60);
    }

    public static function fromUUIDv1 (string $uuid) : self {
        $uuidBits = BitsVector::fromDecoratedHexString($uuid);
        $timestamp = new self();

        // Reads 60 bits timestamp from UUIDv1
        //              UUIDv1      Timestamp
        //  time_low     0-31       28-59
        //  time_mid    32-47       12-27
        //  time_high   52-63        0-11
        $timestamp->bits
            ->replace($uuidBits->slice(52, 12), 0, 12)
            ->replace($uuidBits->slice(32, 16), 12, 16)
            ->replace($uuidBits, 28, 32);

        return $timestamp;
    }

    public static function fromUUIDv6 (string $uuid) : self {
        $uuidBits = BitsVector::fromDecoratedHexString($uuid);
        $timestamp = new self();

        //             Timestamp    UUIv6
        //  time_high   0-31         0-31
        //  time_mid   32-47        32-47
        //  time_low   48-60        52-63
        $timestamp->bits
            ->replace($uuidBits, 0, 32)
            ->replace($uuidBits->slice(32, 16), 32, 16)
            ->replace($uuidBits->slice(52, 12), 48, 12);

        return $timestamp;
    }

    public static function fromBits (BitsVector $bits) : self {
        if ($bits->count() !== 60) {
            throw new InvalidArgumentException("Timestamp must be 60 bits.");
        }

        return new self($bits);
    }

    public static function fromTimeStamp (int $timestamp) : self {
        $bits = BitsVector::new(60)
                          ->copyInteger($timestamp, 0, 60);

        return new self($bits);
    }

    public static function fromUnixTime (float|int $time) : self {
        $timestamp = (int)($time * 1E7)
                   + self::OFFSET_BETWEEN_GREGORIAN_AND_UNIX_EPOCH;

        return self::fromTimeStamp($timestamp);
    }

    public static function fromDateTime(DateTimeInterface $dateTime) : self {
        return self::fromUnixTime($dateTime->getTimestamp());
    }

    public static function now () : self {
        return self::fromUnixTime(microtime(true));
    }

    ///
    /// Helper methods
    ///

    public function writeToUUIDv1 (BitsVector $uuidBits) : void {
        // Write 60 bits timestamp to UUIDv1
        //              UUIDv1      Timestamp
        //  time_low     0-31       28-59
        //  time_mid    32-47       12-27
        //  time_high   52-63        0-11
        $uuidBits
            ->replace($this->bits->slice(28, 32), 0, 32)
            ->replace($this->bits->slice(12, 16), 32, 16)
            ->replace($this->bits, 52, 12);
    }

    public function writeToUUIDv6 (BitsVector $uuidBits) : void {
        // Write 60 bits timestamp to UUIDv6
        //             Timestamp    UUIv6
        //  time_high   0-31         0-31
        //  time_mid   32-47        32-47
        //  time_low   48-59        52-63
        $uuidBits
            ->replace($this->bits, 0, 32)
            ->replace($this->bits->slice(32, 16), 32, 16)
            ->replace($this->bits->slice(48, 12), 52, 12);
    }

    ///
    /// Properties
    ///

    public function toBitsVector () : BitsVector {
        return $this->bits;
    }

    public function toUnixTime () : int {
        return (int)floor(
            ($this->bits->toInteger() - self::OFFSET_BETWEEN_GREGORIAN_AND_UNIX_EPOCH) / 1E7
        );
    }

}
