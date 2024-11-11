<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Strings\Multibyte;

use Keruald\OmniTools\Collections\Vector;

/**
 * Represents a multibyte string and perform operations with the grapheme
 * library for UTF-8 encoding, and mbstring for other encodings.
 */
class OmniString {

    use WithEncoding;

    ///
    /// Private members
    ///

    private string $value;

    ///
    /// Constructor
    ///

    public function __construct (string $value = '', string $encoding = '') {
        $this->value = $value;
        $this->setEncoding($encoding ?: "UTF-8");
    }

    ///
    /// Magic methods
    ///

    public function __toString() : string {
        return $this->value;
    }

    ///
    /// Helper methods
    ///

    public function pad(
        int $padLength = 0,
        string $padString = ' ',
        int $padType = STR_PAD_RIGHT
    ) : string {
        return (new StringPad)
            ->setInput($this->value)
            ->setEncoding($this->encoding)
            ->setPadLength($padLength)
            ->setPadString($padString)
            ->setPadType($padType)
            ->pad();
    }

    public function startsWith (string $start) : bool {
        return str_starts_with($this->value, $start);
    }

    public function endsWith (string $end) : bool {
        return str_ends_with($this->value, $end);
    }

    /**
     * @deprecated Use more specific method to express your intent:
     *             countBytes, countCodePoints or countGraphemes
     */
    public function len () : int {
        return $this->countGraphemes();
    }

    public function countBytes () : int {
        return strlen($this->value);
    }

    public function countCodePoints () : int {
        return mb_strlen($this->value, $this->encoding);
    }

    public function countGraphemes () : int {
        return match ($this->encoding) {
            "UTF-8" => grapheme_strlen($this->value),
            default => $this->countCodepoints(),
        };
    }

    public function getBytes() : array {
        return str_split($this->value, 1);
    }

    public function getCodePoints () : array {
        return mb_str_split($this->value, 1, $this->encoding);
    }

    public function getGraphemes () : array {
        if ($this->encoding !== "UTF-8") {
            return $this->getCodePoints();
        }

        $chars = [];

        $len = grapheme_strlen($this->value);
        for ($i = 0 ; $i < $len ; $i++) {
            $chars[] = grapheme_substr($this->value, $i, 1);
        }

        return $chars;
    }

    /**
     * @deprecated Use more specific method to express your intent:
     *             getBytes, getCodePoints or getGraphemes
     */
    public function getChars () : array {
        return $this->getGraphemes();
    }

    public function getBigrams () : array {
        return match ($this->encoding) {
            "UTF-8" => $this->getBigramsFromGraphemes(),
            default => $this->getBigramsFromCodePoints(),
        };
    }

    private function getBigramsFromGraphemes() : array {
        $bigrams = [];

        $len = grapheme_strlen($this->value);
        for ($i = 0 ; $i < $len - 1 ; $i++) {
            $bigrams[] = grapheme_substr($this->value, $i, 2);
        }

        return $bigrams;
    }

    private function getBigramsFromCodePoints() : array {
        $bigrams = [];

        $len = mb_strlen($this->value, $this->encoding);
        for ($i = 0 ; $i < $len - 1 ; $i++) {
            $bigrams[] = mb_substr($this->value, $i, 2, $this->encoding);
        }

        return $bigrams;
    }

    ///
    /// Transformation methods
    ///

    public function explode (string $delimiter,
                             int $limit = PHP_INT_MAX) : Vector {
        if ($delimiter === "") {
            if ($limit < 0) {
                return new Vector;
            }

            return new Vector([$this->value]);
        }

        return new Vector(explode($delimiter, $this->value, $limit));
    }

    ///
    /// Getters and setters
    ///

    /**
     * @return string
     */
    public function getValue () : string {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue (string $value) : void {
        $this->value = $value;
    }

}
