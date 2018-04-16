<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Strings\Multibyte;

use InvalidArgumentException;

class StringPad {

    use WithEncoding;

    ///
    /// Private members for user-defined or default values
    ///

    /**
     * @var string
     */
    private $input;

    /**
     * @var int
     */
    private $padLength;

    /**
     * @var string
     */
    private $padString;

    /**
     * @var int
     */
    private $padType;

    ///
    /// Private members for computed values
    ///

    /**
     * @var string
     */
    private $repeatedString;

    /**
     * @var float
     */
    private $targetLength;

    ///
    /// Constructor
    ///

    public function __construct (
        string $input = '',
        int $padLength = 0,
        string $padString = ' ',
        int $padType = STR_PAD_RIGHT,
        ?string $encoding = null
    ) {
        $this->input = $input;
        $this->padLength = $padLength;
        $this->padString = $padString;

        $this->setPadType($padType);
        $this->setEncoding($encoding ?? mb_internal_encoding());
    }

    ///
    /// Getters and setters
    ///

    public function getInput () : string {
        return $this->input;
    }

    public function setInput (string $input) : StringPad {
        $this->input = $input;

        return $this;
    }

    public function getPadLength () : int {
        return $this->padLength;
    }

    public function setPadLength (int $padLength) : StringPad {
        $this->padLength = $padLength;

        return $this;
    }

    public function getPadString () : string {
        return $this->padString;
    }

    public function setPadString (string $padString) : StringPad {
        $this->padString = $padString;

        return $this;
    }

    public function getPadType () : int {
        return $this->padType;
    }

    public function setPadType (int $padType) : StringPad {
        if (!self::isValidPadType($padType)) {
            throw new InvalidArgumentException;
        }

        $this->padType = $padType;

        return $this;
    }

    ///
    /// Helper methods to get and set
    ///

    public function setBothPad () : StringPad {
        $this->padType = STR_PAD_BOTH;

        return $this;
    }

    public function setLeftPad () : StringPad {
        $this->padType = STR_PAD_LEFT;

        return $this;
    }

    public function setRightPad () : StringPad {
        $this->padType = STR_PAD_RIGHT;

        return $this;
    }

    public static function isValidPadType (int $padType) : bool {
        return $padType >= 0 && $padType <= 2;
    }

    ///
    /// Pad methods
    ///

    public function pad () : string {
        $this->computeLengths();
        return $this->getLeftPad() . $this->input . $this->getRightPad();
    }

    private function getLeftPad () : string {
        if (!$this->hasPaddingBefore()) {
            return '';
        }

        $length = (int)floor($this->targetLength);
        return mb_substr($this->repeatedString, 0, $length, $this->encoding);
    }

    private function getRightPad () : string {
        if (!$this->hasPaddingAfter()) {
            return '';
        }

        $length = (int)ceil($this->targetLength);
        return mb_substr($this->repeatedString, 0, $length, $this->encoding);
    }

    private function computeLengths () : void {
        $this->targetLength   = $this->computeNeededPadLength();
        $this->repeatedString = $this->computeRepeatedString();
    }

    private function computeRepeatedString () : string {
        // Inspired by Ronald Ulysses Swanson method
        // https://stackoverflow.com/a/27194169/1930997
        // who followed the str_pad PHP implementation.

        $strToRepeatLength = mb_strlen($this->padString, $this->encoding);
        $repeatTimes = (int)ceil($this->targetLength / $strToRepeatLength);

        // Safe if used with valid Unicode sequences (any charset).
        return str_repeat($this->padString, max(0, $repeatTimes));
    }

    private function computeNeededPadLength () : float {
        $length = $this->padLength - mb_strlen($this->input, $this->encoding);

        if ($this->hasPaddingBeforeAndAfter()) {
            return $length / 2;
        }

        return $length;
    }

    private function hasPaddingBefore () : bool {
        return $this->padType === STR_PAD_LEFT || $this->padType === STR_PAD_BOTH;
    }

    private function hasPaddingAfter () : bool {
        return $this->padType === STR_PAD_RIGHT || $this->padType === STR_PAD_BOTH;
    }

    private function hasPaddingBeforeAndAfter () : bool {
        return
            $this->padType === STR_PAD_BOTH
            ||
            ($this->padType === STR_PAD_LEFT && $this->padType === STR_PAD_RIGHT)
        ;
    }

}
