<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Reflection;

class CodeVariable {

    private mixed $variable;

    ///
    /// Constructor
    ///

    public static function from (mixed $variable) : self {
        $instance = new self;
        $instance->variable = $variable;

        return $instance;
    }

    ///
    /// Type helper methods
    ///

    public function hasType (string $type) : bool {
        $ourType = gettype($this->variable);

        // For scalar types, gettype() doesn't return the same types
        // as does reflection classes.
        return match ($ourType) {
            "boolean" => $type === "bool" || $type === "boolean",
            "integer" => $type === "int" || $type === "integer",
            "double" => $type === "float" || $type === "double",
            "object" => $this->variable::class === $type,
            default => $ourType === $type,
        };
    }

    public function getType () : string {
        $type = gettype($this->variable);

        // For scalar types, gettype() doesn't return the same types
        // as does reflection classes.
        return match ($type) {
            "boolean" => "bool",
            "integer" => "int",
            "double" => "float",
            "object" => $this->variable::class,
            default => $type,
        };
    }

}
