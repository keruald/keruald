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

    public function __construct (string $value = '', ?string $encoding = null) {
        $this->value = $value;
        $this->setEncoding($encoding ?? "UTF-8");
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
