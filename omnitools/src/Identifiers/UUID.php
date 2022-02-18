<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Identifiers;

class UUID {

    const UUID_REGEXP = "/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/";

    /**
     * @return string An RFC 4122 compliant v4 UUID
     */
    public static function UUIDv4 () : string {
        // Code by Andrew Moore
        // See http://php.net/manual/en/function.uniqid.php#94959
        //     https://www.ietf.org/rfc/rfc4122.txt

        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

            // 32 bits for "time_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),

            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    public static function UUIDv4WithoutHyphens () : string {
        return str_replace("-", "", self::UUIDv4());
    }

    public static function isUUID ($string) : bool {
        return (bool)preg_match(self::UUID_REGEXP, $string);
    }

}
