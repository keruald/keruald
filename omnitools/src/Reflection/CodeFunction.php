<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Reflection;

use InvalidArgumentException;
use ReflectionParameter;

class CodeFunction {

    /**
     * @throws InvalidArgumentException
     */
    public static function getParameterType (ReflectionParameter $parameter) : string {
        if (!$parameter->hasType()) {
            $name = $parameter->getName();
            throw new InvalidArgumentException(
                "Parameter $name doesn't have a type"
            );
        }

        return $parameter->getType()->getName();
    }

}
