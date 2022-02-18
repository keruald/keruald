<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Strings\Multibyte;

use Keruald\OmniTools\Collections\Vector;

class OmniString {

    use WithEncoding;

    ///
    /// Private members
    ///

    /**
     * @var string
     */
    private $value;

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

    public function len () : int {
        return mb_strlen($this->value, $this->encoding);
    }

    public function getChars () : array {
        $chars = [];

        $len = $this->len();
        for ($i = 0 ; $i < $len ; $i++) {
            $chars[] = mb_substr($this->value, $i, 1, $this->encoding);
        }

        return $chars;
    }

    public function getBigrams () : array {
        $bigrams = [];

        $len = $this->len();
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
