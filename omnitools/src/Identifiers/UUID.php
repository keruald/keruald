<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Identifiers;

use Exception;
use InvalidArgumentException;

use Keruald\OmniTools\Collections\BitsVector;
use Keruald\OmniTools\Collections\Vector;
use Keruald\OmniTools\DateTime\UUIDv1TimeStamp;
use Keruald\OmniTools\DateTime\UUIDv7TimeStamp;

/**
 * Allow generating and representing UUID by implementing both RFC 4122
 * and proposed extension to UUIDv6, UUIDv7 and UUIDv8.
 *
 * A UUID is a universal identified with good local and global uniqueness
 * on the form xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx where x is hexadecimal.
 *
 * To generate a random identified, you can use `UUID::UUIDv4()`.
 *
 * When you need a monotonic series of always growing identifiers,
 * you can call `UUID::UUIDv7()`, time-dependent, with 74 bits of randomness,
 * or `UUID::batchOfUUIDv7(10)`, with at least 62 bits of randomness warranted.
 */
class UUID {

    /**
     * A regular expression to detect if a lowercase string is a valid UUID.
     */
    public const UUID_REGEXP = "/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/";

    /**
     * The maximal signed integer representable in 12 bits.
     */
    private const MAX_12 = 4095;

    /**
     * The maximal signed integer representable in 48 bits.
     */
    private const MAX_48 = 281_474_976_710_655;

    /**
     * The maximal signed integer representable in 62 bits.
     */
    private const MAX_62 = 4_611_686_018_427_387_903;

    /**
     * The quantity of UUIDv7 in a batch allowed to share the same timestamp.
     */
    private const UUIDV7_QUANTITY_PER_MS = 63;

    ///
    /// Public constants from RFC 4122 and draft-peabody-dispatch-new-uuid-format-03
    ///

    /**
     * A null value for a UUID, as defined in RFC 4122.
     */
    public const NIL = "00000000-0000-0000-0000-000000000000";

    /**
     * The maximum value for a UUID.
     */
    public const MAX = "ffffffff-ffff-ffff-ffff-ffffffffffff";

    ///
    /// RFC 4122 - UUIDv1
    ///

    /**
     * Generate a UUIDv1, as defined in RFC 4122.
     *
     * @param int    $clk_seq_hi_res
     * @param int    $clk_seq_low
     * @param string $mac The node information, normally the MAC address ; if
     *                    omitted, a random value will be generated.
     *
     * @return string The UUID
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

    /**
     * Generate a UUIDv1, as defined in RFC 4122, from specified values.
     *
     * That method can be used to reproduce a UUID from known parameters.
     *
     * @param UUIDv1TimeStamp $timestamp A 60 bits timestamp
     * @param int             $clk_seq_hi_res A 6 bits signed integer
     * @param int             $clk_seq_low A 8 bits signed integer
     * @param BitsVector      $node A 48 bits vector
     *
     * @return string The UUID
     */
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
     * Generate a UUIDv4, as defined in RFC 4122.
     *
     * This UUID offers 122 bits of randomness, built from cryptographically
     * secure pseudo-random integers.
     *
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

    /**
     * Generate a UUIDv4, as defined in RFC 4122, without hyphens.
     *
     * @see UUID::UUIDv4()
     * @return string
     * @throws Exception
     */
    public static function UUIDv4WithoutHyphens () : string {
        return str_replace("-", "", self::UUIDv4());
    }

    ///
    /// draft-peabody-dispatch-new-uuid-format-03 - UUIDv6
    ///

    /**
     * Generate a UUIDv6, as defined in draft-peabody-dispatch-new-uuid-format-03.
     *
     * This format is similar to UUIDv1, with bits reordered to allow
     * monotonicity. It is mainly designed to use when compatibility with
     * UUIDv1 is required. For new systems, UUIDv7 use is recommended.
     *
     * This UUID is deterministic, built from a 60 bits timestamp, a clock
     * sequence and a node information. It doesn't contain any source of
     * randomness, excepted if node information is replaced by 48 random bits,
     * and as such, isn't suitable to be used to generate credentials,
     * or an identified difficult to guess ; use UUIDv7 or UUIDv4 in such cases.
     *
     * The RFC 4122 recommends the use of the MAC address when available,
     * as a good way to ensure global uniqueness of the UUID. Such use will leak
     * your MAC address, while the warranty of global uniqueness will be false
     * if the MAC address is spoofed, or automatically generated for a VM.
     * As such, proposed draft don't recommend to use MAC address anymore.
     *
     * @param int    $clk_seq_hi_res
     * @param int    $clk_seq_low
     * @param string $mac The node information, normally the MAC address ; if
     *                    omitted, a random value will be generated.
     *
     * @return string The UUID
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

    /**
     * Generate a UUIDv6, as defined in draft-peabody-dispatch-new-uuid-format-03,
     * from known values.
     *
     * @see UUID::UUIDv6()
     *
     * @param UUIDv1TimeStamp $timestamp A 60 bits precision timestamp
     * @param int             $clk_seq_hi_res
     * @param int             $clk_seq_low
     * @param BitsVector      $node A 48 bits vector to identify the node
     *
     * @return string The UUID
     * @throws Exception if $mac is not specified, and an appropriate source of randomness cannot be found.
     * @throws InvalidArgumentException if $mac is specified and doesn't contain exactly 12 hexadecimal characters.
     */
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

    /**
     * Convert RFC 4122 UUIDv1 to proposed draft UUIDv6.

     * @param string $uuid The UUIDv1 to convert
     * @return string A UUIDv6 with the same information as the UUIDv1.
     */
    public static function UUIDv1ToUUIDv6 (string $uuid) : string {
        $bits = BitsVector::fromDecoratedHexString($uuid);
        UUIDv1TimeStamp::fromUUIDv1($uuid)->writeToUUIDv6($bits);

        // Version 6 for UUIDv6, bits 48-51
        $bits->copyInteger(6, 48, 4);

        return self::reformat($bits->toHexString());
    }

    /**
     * Convert proposed draft UUIDv6 to RFC 4122 UUIDv1.

     * @param string $uuid The UUIDv6 to convert
     * @return string A UUIDv1 with the same information as the UUIDv6.
     */
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
     * Generate a UUIDv7, as defined in draft-peabody-dispatch-new-uuid-format-03.
     *
     * This UUID associates a 48 bits timestamp to 74 bits of randomness.
     *
     * When called at 1 ms intervals, it gives a monotonicity warranty, ie each
     * UUID generated will be greater than the previous one. When you need
     * several UUIDv7 immediately, use `UUID::batchOfUUIDv7($count)` to get
     * the same warranty.
     *
     * @throws Exception if an appropriate source of randomness cannot be found.
     * @see UUID::batchOfUUIDv7() when you need a monotonic series of UUIDv7
     */
    public static function UUIDv7 () : string {
        return self::UUIDv7FromBits(
            UUIDv7TimeStamp::now()->toBitsVector(),
            random_int(0, self::MAX_12),
            random_int(0, self::MAX_62),
        );
    }

    /**
     * Generate in batch UUIDv7 with monotonicity warranty among them.
     *
     * UUID in small batches  share the same timestamp,
     * but to maintain some bits of randomness and enough entropy,
     * a new timestamp will be used every 64 timestamps.
     *
     * When generating a very large batch (> 10000), this method will be slow,
     * as 1 ms break is needed every 64 timestamps, and random_int() can also
     * wait for a source of entropy.
     *
     * @param int $count The number of UUIDv7 to generate
     * @return string[] An array of UUIDv7 with monotonic warranty.
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

    /**
     * Generate a UUIDv7 for known timestamp and known random values.
     *
     * @param BitsVector $unixTimestampMs A 48 bits timestamp
     * @param int        $randA A 12 bits value for random A number
     * @param int        $randB A 62 bits value for random B number
     *
     * @return string The UUIDv7
     */
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

    /**
     * Allow to generate a UUIDv7 for known timestamp and known random values,
     * when the timestamp is available as a signed 48-bits integer.
     *
     * @param int $unixTimestampMs A 48 bits signed integer for timestamp
     * @param int $randA A 12 bits value for random A number
     * @param int $randB A 62 bits value for random B number
     *
     * @return string The UUIDv7
     */
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
     * According to proposed draft, the UUIDv8 lets the implementation decides
     * of the bits' layout.  Accordingly, this method give you the control
     * of the data you want to use in the UUID. It will write specified values
     * like big-endian signed numbers.
     *
     * @param int $a A 48 bits integer for custom_a field
     * @param int $b A 12 bits integer for custom_b field
     * @param int $c A 62 bits integer for custom_c field
     * @return string The generated UUID
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

    /**
     * Reformat a 32 or 36 hexadecimal string to a lowercase 36 uuid string.
     *
     * @param string $uuid a hexadecimal string with or without hyphens
     * @return string A formatted UUID.
     */
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

    /**
     * Determine if the specified string is a valid UUID
     *
     * @param string A well-formatted, lowercase string
     * @return bool
     */
    public static function isUUID ($string) : bool {
        return (bool)preg_match(self::UUID_REGEXP, $string);
    }

    /**
     * Determine the version of the specified UUID.
     *
     * Normally, the proposed draft recommends treating UUID as an opaque value
     * and refrain to inspect bits. However, where necessary, inspectors method
     * for version and variants are allowed.
     *
     * @param string $uuid
     * @return int The UUID version
     */
    public static function getVersion (string $uuid) : int {
        // bits 48 -> 51 represent version
        return BitsVector::fromDecoratedHexString($uuid)
                         ->slice(48, 4)
                         ->toInteger();
    }

    /**
     * Determine the variant of the specified UUID.
     *
     * Normally, the proposed draft recommends treating UUID as an opaque value
     * and refrain to inspect bits. However, where necessary, inspectors method
     * for version and variants are allowed.
     *
     * @param string $uuid
     * @return int The UUID variant
     */
    public static function getVariant (string $uuid) : int {
        // bits 64 -> 65 represent variant
        return BitsVector::fromDecoratedHexString($uuid)
                         ->slice(64, 2)
                         ->toInteger();
    }

}
