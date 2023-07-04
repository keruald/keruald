<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Booleans;

readonly class Boolean {

    ///
    /// Properties
    ///

    private bool $value;

    ///
    /// Constructors
    ///

    public function __construct (bool $value) {
        $this->value = $value;
    }

    public static function true () : self {
        return new self(true);
    }

    public static function false () : self {
        return new self(false);
    }

    ///
    /// Basic logic operators
    ///

    public function and (self|bool $other) : self {
        $newValue = match($this->value) {
            true => self::toScalar($other),
            false => false,
        };

        return new self($newValue);
    }

    public function or (self|bool $other) : self {
        $newValue = match($this->value) {
            true => true,
            false => self::toScalar($other),
        };

        return new self($newValue);
    }

    public function xor (self|bool $other) : self {
        $newValue = ($this->value xor self::toScalar($other));

        return new self($newValue);
    }

    public function not () : self {
        return new self(!$this->value);
    }

    public function implication (self|bool $other) : self {
        $newValue = $this->value === false || self::toScalar($other);

        return new self($newValue);
    }

    public function equivalence (self|bool $other) : self {
        return new self($this->isEqualsTo($other));
    }

    ///
    /// Comparison
    ///

    public function isEqualsTo (self|bool $other) : bool {
        return $this->value === self::toScalar($other);
    }

    ///
    /// Type convert
    ///

    public function asBool () : bool {
        return $this->value;
    }

    public function asInteger () : int {
        return (int)$this->value;
    }

    public function asString () : string {
        return match ($this->value) {
            true => "true",
            false => "false",
        };
    }

    public static function toScalar (self|bool $bool) : bool {
        if ($bool instanceof self) {
            return $bool->value;
        }

        return (bool)$bool;
    }

}
