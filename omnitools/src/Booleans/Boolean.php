<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Booleans;

class Boolean {

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
        if ($this->value === true) {
            $this->value = self::toScalar($other);
        }

        return $this;
    }

    public function or (self|bool $other) : self {
        if ($this->value === false) {
            $this->value = self::toScalar($other);
        }

        return $this;
    }

    public function xor (self|bool $other) : self {
        $this->value = ($this->value xor self::toScalar($other));

        return $this;
    }

    public function not () : self {
        $this->value = !$this->value;

        return $this;
    }

    public function implication (self|bool $other) : self {
        $this->value = $this->value === false || self::toScalar($other);

        return $this;
    }

    public function equivalence (self|bool $other) : self {
        $this->value = $this->isEqualsTo($other);

        return $this;
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
