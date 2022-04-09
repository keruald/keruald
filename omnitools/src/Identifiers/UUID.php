<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Identifiers;

use Exception;
use InvalidArgumentException;

use Keruald\OmniTools\Collections\BitsVector;
use Keruald\OmniTools\Collections\Vector;
use Keruald\OmniTools\DateTime\UUIDv1TimeStamp;
use Keruald\OmniTools\DateTime\UUIDv7TimeStamp;

class UUID {

    const UUID_REGEXP = "/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/";

    const MAX_12 = 4095;
    const MAX_48 = 281_474_976_710_655;
    const MAX_62 = 4_611_686_018_427_387_903;

    const UUIDV7_QUANTITY_PER_MS = 63;

    ///
    /// Public constants from RFC 4122 and draft-peabody-dispatch-new-uuid-format-03
    ///

    public const NIL = "00000000-0000-0000-0000-000000000000";
    public const MAX = "ffffffff-ffff-ffff-ffff-ffffffffffff";

    ///
    /// RFC 4122 - UUIDv1
    ///

    /**
     * @param int    $clk_seq_hi_res
     * @param int    $clk_seq_low
     * @param string $mac The node information, normally the MAC address ; if
     *                    omitted, a random value will be generated.
     *
     * @return string
     * @throws Exception if $mac is not specified, and an appropriate source of randomness cannot be found.
     * @throws InvalidArgumentException if $mac is specified and doesn't contain exactly 12 hexadecimal characters.
     */
    public static function UUIDv1 (
        string          $mac = "",
        int             $clk_seq_hi_res = 0,
        int             $clk_seq_low = 0,
    ) : string {
        $node = match ($mac) {
            "" => BitsVector::random(48),
            default => BitsVector::fromDecoratedHexString($mac),
        };

        return self::UUIDv1FromValues(
            UUIDv1TimeStamp::now(),
            $clk_seq_hi_res,
            $clk_seq_low,
            $node,
        );
    }

    public static function UUIDv1FromValues (
        UUIDv1TimeStamp $timestamp,
        int             $clk_seq_hi_res,
        int             $clk_seq_low,
        BitsVector      $node,
    ) : string {
        if ($node->count() !== 48) {
            throw new InvalidArgumentException("Node information must be 48 bits, ideally from a 12 characters hexadecimal MAC address string.");
        }

        $bits = BitsVector::new(128);

        $timestamp->writeToUUIDv1($bits);
        $bits->copyInteger(1, 48, 4); // version 1 from UUIDv1
        $bits->copyInteger(2, 64, 2); // variant 2
        $bits->copyInteger($clk_seq_hi_res, 66, 6);
        $bits->copyInteger($clk_seq_low, 72, 8);
        $bits->replace($node, 80, 48);

        return self::reformat($bits->toHexString());
    }

    ///
    /// RFC 4122 - UUIDv4
    ///

    /**
     * @return string An RFC 4122 compliant v4 UUID
     * @throws Exception if an appropriate source of randomness cannot be found.
     */
    public static function UUIDv4 () : string {
        // Code by Andrew Moore
        // See http://php.net/manual/en/function.uniqid.php#94959
        //     https://www.ietf.org/rfc/rfc4122.txt

        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

            // 32 bits for "time_low"
            random_int(0, 0xffff), random_int(0, 0xffff),

            // 16 bits for "time_mid"
            random_int(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            random_int(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            random_int(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            random_int(0, 0xffff), random_int(0, 0xffff), random_int(0, 0xffff)
        );
    }

    public static function UUIDv4WithoutHyphens () : string {
        return str_replace("-", "", self::UUIDv4());
    }

    ///
    /// draft-peabody-dispatch-new-uuid-format-03 - UUIDv6
    ///

    /**
     * @param int    $clk_seq_hi_res
     * @param int    $clk_seq_low
     * @param string $mac The node information, normally the MAC address ; if
     *                    omitted, a random value will be generated.
     *
     * @return string
     * @throws Exception if $mac is not specified, and an appropriate source of randomness cannot be found.
     * @throws InvalidArgumentException if $mac is specified and doesn't contain exactly 12 hexadecimal characters.
     */
    public static function UUIDv6 (
        string          $mac = "",
        int             $clk_seq_hi_res = 0,
        int             $clk_seq_low = 0,
    ) : string {
        $node = match ($mac) {
            "" => BitsVector::random(48),
            default => BitsVector::fromDecoratedHexString($mac),
        };

        return self::UUIDv6FromValues(
            UUIDv1TimeStamp::now(),
            $clk_seq_hi_res,
            $clk_seq_low,
            $node,
        );
    }

    public static function UUIDv6FromValues (
        UUIDv1TimeStamp $timestamp,
        int             $clk_seq_hi_res,
        int             $clk_seq_low,
        BitsVector      $node,
    ) : string {
        if ($node->count() !== 48) {
            throw new InvalidArgumentException("Node information must be 48 bits, ideally from a 12 characters hexadecimal MAC address string.");
        }

        $bits = BitsVector::new(128);

        $timestamp->writeToUUIDv6($bits);
        $bits->copyInteger(6, 48, 4); // version 6 from UUIDv6
        $bits->copyInteger(2, 64, 2); // variant 2
        $bits->copyInteger($clk_seq_hi_res, 66, 6);
        $bits->copyInteger($clk_seq_low, 72, 8);
        $bits->replace($node, 80, 48);

        return self::reformat($bits->toHexString());
    }

    public static function UUIDv1ToUUIDv6 (string $uuid) : string {
        $bits = BitsVector::fromDecoratedHexString($uuid);
        UUIDv1TimeStamp::fromUUIDv1($uuid)->writeToUUIDv6($bits);

        // Version 6 for UUIDv6, bits 48-51
        $bits->copyInteger(6, 48, 4);

        return self::reformat($bits->toHexString());
    }

    public static function UUIDv6ToUUIDv1 (string $uuid) : string {
        $bits = BitsVector::fromDecoratedHexString($uuid);
        UUIDv1TimeStamp::fromUUIDv6($uuid)->writeToUUIDv1($bits);

        // Version 1 for UUIDv6, bits 48-51
        $bits->copyInteger(1, 48, 4);

        return self::reformat($bits->toHexString());
    }

    ///
    /// draft-peabody-dispatch-new-uuid-format-03 - UUIDv7
    ///

    /**
     * @throws Exception if an appropriate source of randomness cannot be found.
     *@see UUID::batchOfUUIDv7()
     */
    public static function UUIDv7 () : string {
        return self::UUIDv7FromBits(
            UUIDv7TimeStamp::now()->toBitsVector(),
            random_int(0, self::MAX_12),
            random_int(0, self::MAX_62),
        );
    }

    /**
     * A batch of UUIDv7 with monotonicity warranty.
     *
     * @param int $count The number of UUIDv7 to generate
     *
     * @return array
     */
    public static function batchOfUUIDv7 (int $count) : array {
        if ($count > self::UUIDV7_QUANTITY_PER_MS) {
            // We only have 12 bits available in random A.
            // Divide in smaller batches to avoid to touch random B.

            $batch = [];
            $stillToGenerateCount = $count;
            while ($stillToGenerateCount > 0) {
                $n = min($stillToGenerateCount, self::UUIDV7_QUANTITY_PER_MS);
                array_push($batch, ...self::batchOfUUIDv7($n));

                $stillToGenerateCount -= self::UUIDV7_QUANTITY_PER_MS;
                usleep(1000); // That will increment the timestamp.
            }
            return $batch;
        }

        $timestamp = UUIDv7TimeStamp::now()->toBitsVector();
        return self::getSeriesRandomA($count)
                     ->map(fn($a) => self::UUIDv7FromBits(
                         $timestamp,
                         $a,
                         random_int(0, self::MAX_62),
                     ))
                     ->toArray();
    }

    private static function getSeriesRandomA (int $count) : Vector {
        return Vector::from(Random::generateIntegerMonotonicSeries(
            0, self::MAX_12, $count
        ));
    }


    public static function UUIDv7FromBits (
        BitsVector $unixTimestampMs,
        int        $randA,
        int        $randB
    ) : string {
        if ($unixTimestampMs->count() != 48) {
            throw new InvalidArgumentException("UUIDv7 timestamps MUST be 48 bits long.");
        }

        $bits = BitsVector::new(128)
                          ->replace($unixTimestampMs, 0, 48)
                          ->copyInteger($randA, 52, 12)
                          ->copyInteger($randB, 66, 62)
                          ->copyInteger(7, 48, 4)  // version (bits 48 -> 51)
                          ->copyInteger(2, 64, 2); // variant (bits 64 -> 65)

        return self::reformat($bits->toHexString());
    }

    public static function UUIDv7FromValues (
        int $unixTimestampMs,
        int $randA,
        int $randB
    ) : string {
        $bits = UUIDv7TimeStamp::fromInteger($unixTimestampMs)->toBitsVector();

        return self::UUIDv7FromBits($bits, $randA, $randB);
    }

    ///
    /// draft-peabody-dispatch-new-uuid-format-03 - UUIDv6
    ///

    /**
     * Generate a UUIDv8 with three custom values.
     *
     * The UUIDv8 lets the implementation decide of the bits' layout.
     * This implementation will write values like big-endian unsigned numbers.
     */
    public static function UUIDv8 (int $a, int $b, int $c) : string {
        if ($a > self::MAX_48) {
            throw new InvalidArgumentException("custom_a field is limited to 48 bits.");
        }

        if ($b > self::MAX_12) {
            throw new InvalidArgumentException("custom_b field is limited to 12 bits.");
        }

        if ($c > self::MAX_62) {
            throw new InvalidArgumentException("custom_c field is limited to 62 bits.");
        }

        $bits = BitsVector::new(128)
                          ->copyInteger($a, 0, 48)   // bits  0 ->  47
                          ->copyInteger($b, 52, 12)  // bits 52 ->  63
                          ->copyInteger($c, 66, 62); // bits 66 -> 127

        $bits[48] = 1; // bits 48 -> 51 represent version 8 (1000)
        $bits[64] = 1; // bits 64 -> 65 represent variant 2 (10)

        return self::reformat($bits->toHexString());
    }

    ///
    /// Helper methods
    ///

    public static function reformat (string $uuid) : string {
        $uuid = strtolower($uuid);

        return match (strlen($uuid)) {
            32 => implode("-", [
                substr($uuid, 0, 8),
                substr($uuid, 8, 4),
                substr($uuid, 12, 4),
                substr($uuid, 16, 4),
                substr($uuid, 20, 12),
            ]),
            36 => $uuid,
            default => throw new InvalidArgumentException("UUID must be 32 or 36 characters long."),
        };
    }

    public static function isUUID ($string) : bool {
        return (bool)preg_match(self::UUID_REGEXP, $string);
    }

    public static function getVersion (string $uuid) : int {
        // bits 48 -> 51 represent version
        return BitsVector::fromDecoratedHexString($uuid)
                         ->slice(48, 4)
                         ->toInteger();
    }

    public static function getVariant (string $uuid) : int {
        // bits 64 -> 65 represent variant
        return BitsVector::fromDecoratedHexString($uuid)
                         ->slice(64, 2)
                         ->toInteger();
    }

}
