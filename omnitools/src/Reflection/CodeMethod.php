<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Reflection;

use Keruald\OmniTools\Collections\Vector;

use ReflectionMethod;
use ReflectionParameter;

class CodeMethod extends CodeFunction {

    ///
    /// Properties
    ///

    private ReflectionMethod $method;

    ///
    /// Constructor
    ///

    public static function fromReflectionMethod (ReflectionMethod $method) : self {
        $instance = new self;
        $instance->method = $method;

        return $instance;
    }

    ///
    /// Arguments helper methods
    ///

    public function getArgumentsType () : Vector {
        return Vector::from($this->method->getParameters())
                     ->map(fn(ReflectionParameter $param) => CodeFunction::getParameterType($param));
    }

}
