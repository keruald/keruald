<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Reflection;

use Keruald\OmniTools\Collections\Vector;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

class CodeClass {

    ///
    /// CodeClass constructor
    ///

    public function __construct (
        private readonly string $className,
    ) {
    }

    ///
    /// Class name helper methods
    ///

    public function getClassName () : string {
        return $this->className;
    }

    /**
     * @throws ReflectionException
     */
    public function getShortClassName () : string {
        $class = new ReflectionClass($this->className);

        return $class->getShortName();
    }

    ///
    /// Represented class constructor helper methods
    ///

    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function getConstructor () : ReflectionMethod {
        $class = new ReflectionClass($this->className);
        $constructor = $class->getConstructor();

        return match ($constructor) {
            null => throw new InvalidArgumentException(
                "This class doesn't have a constructor."
            ),
            default => $constructor,
        };
    }

    /**
     * @throws ReflectionException
     */
    public function getConstructorArgumentsTypes () : Vector {
        $class = new ReflectionClass($this->className);
        $constructor = $class->getConstructor();

        if ($constructor === null) {
            return new Vector;
        }

        return CodeMethod::fromReflectionMethod($constructor)
            ->getArgumentsType();
    }

    /**
     *
     * This method can be used for dependency injection to build a class,
     * like a controller in a MVC model, from a services' container.
     *
     * Each argument of the constructor is substituted by an item from
     * $services. To match properly services and constructor arguments,
     * each arguments need to have a type, and those types should properly
     * exist in $services, without duplicate.
     *
     * @param iterable $services a collection with keys as type names and values
         * @return object A new instance of the reflected class
     * @throws ReflectionException
     */
    public function newInstanceFromServices (iterable $services) : object {
        $args = $this->getConstructorArgumentsTypes()
            ->map(function (string $type) use ($services) : mixed {
                foreach ($services as $value) {
                    if (CodeVariable::from($value)->hasType($type)) {
                        return $value;
                    }
                }

                throw new InvalidArgumentException("No instance of type $type can be found.");
            });

        $class = new ReflectionClass($this->className);
        return $class->newInstance(...$args);

    }

}
