<?php

namespace Keruald\OmniTools\DataTypes\Option;

use InvalidArgumentException;

use Keruald\OmniTools\Reflection\Type;

class Some extends Option {
    private mixed $value = null;

    private string $type = "NULL";

    public function __construct ($value = null) {
        if ($value !== null) {
            $this->setValue($value);
        }
    }

    public function isSome () : bool {
        return true;
    }

    public function isNone () : bool {
        return false;
    }

    public function getValue () : mixed {
        return $this->value;
    }

    public function setValue (mixed $value) : void {
        $type = Type::getTypeOf($value);
        if (!$this->isAcceptableValueType($type)) {
            throw new InvalidArgumentException(<<<'EOD'
When you mutate the value of an Some object, you can't mutate the object type.
Please consider return a new Some instead.
EOD
            );
        }

        $this->value = $value;
        $this->type = $type;
    }

    private function isAcceptableValueType (string $type) : bool {
        return $this->value === null || $type === $this->type;
    }

    public function map (callable $callable) : Option {
        $value = $callable($this->value);

        return new self($value);
    }

    public function orElse (mixed $default) : mixed {
        return $this->value;
    }
}
