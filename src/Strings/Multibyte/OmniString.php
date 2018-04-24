<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Strings\Multibyte;

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
        return StringUtilities::startsWith($this->value, $start);
    }

    public function endsWith (string $end) : bool {
        return StringUtilities::endsWith($this->value, $end);
    }

    public function len () : int {
        return mb_strlen($this->value, $this->encoding);
    }

    /**
     * @return string
     */
    public function getValue () : string {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue (string $value) {
        $this->value = $value;
    }

}
