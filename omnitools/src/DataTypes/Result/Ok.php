<?php

namespace Keruald\OmniTools\DataTypes\Result;

use InvalidArgumentException;

class Ok extends Result {
    private mixed $value = null;

    private string $type = "NULL";

    public function __construct ($value = null) {
        if ($value !== null) {
            $this->setValue($value);
        }
    }

    public function isOK () : true {
        return true;
    }

    public function isError () : false {
        return false;
    }

    public function getValue () : mixed {
        return $this->value;
    }

    public function setValue (mixed $value) : void {
        $type = self::getTypeOf($value);
        if (!$this->isAcceptableValueType($type)) {
            throw new InvalidArgumentException(<<<'EOD'
When you mutate the value of an Ok object, you can't mutate the object type.
Please consider return a new Ok instead.
EOD
            );
        }

        $this->value = $value;
        $this->type = $type;
    }

    private function isAcceptableValueType (string $type) : bool {
        return $this->value === null || $type === $this->type;
    }


    private static function getTypeOf ($v) : string {
        $type = gettype($v);

        if ($type === "object") {
            return get_class($v);
        }

        return $type;
    }

    public function map (callable $callable) : self {
        $value = $callable($this->value);

        return new self($value);
    }

    public function mapErr (callable $callable) : self {
        return $this;
    }

    public function orElse (mixed $default) : mixed {
        return $this->value;
    }
}
