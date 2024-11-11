<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Strings\Multibyte;

class StringUtilities {

    /**
     * Pads a multibyte string to a certain length with another string
     *
     * @param string $input the input string
     * @param int $padLength the target string size
     * @param string $padString the padding characters (optional, default is space)
     * @param int $padType STR_PAD_RIGHT, STR_PAD_LEFT, or STR_PAD_BOTH (optional, default is STR_PAD_RIGHT)
     * @param string $encoding the character encoding (optional)
     *
     * @return string the padded string
     *
     */
    public static function pad (
        string $input,
        int $padLength,
        string $padString = ' ',
        int $padType = STR_PAD_RIGHT,
        string $encoding = ''
    ) : string {
        return (new StringPad)
            ->setInput($input)
            ->setPadLength($padLength)
            ->setPadString($padString)
            ->setPadType($padType)
            ->setEncoding($encoding ?: "UTF-8")
            ->pad();
    }

    public static function isSupportedEncoding (string $encoding) : bool {
        return match ($encoding) {
            "UTF-8" => true,
            default => in_array($encoding, mb_list_encodings()),
        };
    }

    /**
     * @deprecated Since PHP 8.0, we can replace by \str_starts_with
     */
    public static function startsWith (string $string, string $start) : bool {
        $length = mb_strlen($start);
        return mb_substr($string, 0, $length) === $start;
    }

    /**
     * @deprecated Since PHP 8.0, we can replace by \str_ends_with
     */
    public static function endsWith (string $string, string $end) : bool {
        $length = mb_strlen($end);
        return $length === 0 || mb_substr($string, -$length) === $end;
    }

    /**
     * @deprecated Since PHP 8.0, we can replace by \str_contains
     */
    public static function contains (string $string, string $needle) : bool {
        return str_contains($string, $needle);
    }

    /**
     * Encode a string using a variant of the MIME base64 compatible with URLs.
     *
     * The '+' and '/' characters used in base64 are replaced by '-' and '_'.
     * The '=' padding is removed.
     *
     * @param string $string The string to encode
     * @return string The encoded string
     */
    public static function encodeInBase64 (string $string) : string {
        return str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode($string)
        );
    }

    /**
     * Decode a string encoded with StringUtilities::encodeInBase64
     *
     * @param string $string The string to decode
     * @return string The decoded string
     */
    public static function decodeFromBase64 (string $string) : string {
        $toDecode = str_replace(['-', '_'], ['+', '/'], $string);
        return base64_decode($toDecode);
    }

}
