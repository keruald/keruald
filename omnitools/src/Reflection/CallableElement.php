<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Reflection;

use Closure;
use InvalidArgumentException;
use ReflectionException;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod;

class CallableElement {

    private ReflectionFunctionAbstract $callable;

    /**
     * @throws ReflectionException if the callable does not exist.
     */
    public function __construct (callable $callable) {
        $this->callable = self::getReflectionFunction($callable);
    }

    /**
     * @throws ReflectionException
     */
    private static function getReflectionFunction (callable $callable)
    : ReflectionFunctionAbstract {

        ///
        /// Functions
        ///

        if ($callable instanceof Closure) {
            return new ReflectionFunction($callable);
        }

        ///
        /// Objets and methods
        ///

        if (is_array($callable)) {
            return new ReflectionMethod($callable[0], $callable[1]);
        }

        if (is_object($callable)) {
            // If __invoke() doesn't exist, the objet isn't a callable.
            // Calling this method with such object would throw a TypeError
            // before reaching this par of the code, so it is safe to assume
            // we can correctly call it.
            return new ReflectionMethod([$callable, '__invoke']);
        }

        ///
        /// Hybrid cases
        ///

        if (is_string($callable)) {
            if (!str_contains($callable, "::")) {
                return new ReflectionFunction($callable);
            }

            return new ReflectionMethod($callable);
        }

        throw new InvalidArgumentException(
            "Callable not recognized: " . gettype($callable)
        );
    }

    public function countArguments () : int {
        return $this->callable->getNumberOfParameters();
    }

    /**
     * Determines if the callable has an explicit return type
     * and that return type matches the specified type.
     *
     * Closure, arrow functions MUST declare explicitly the return type
     * in the signature to be able to detect it. It will always be false if not.
     */
    public function hasReturnType (string $type) : bool {
        if (!$this->callable->hasReturnType()) {
            return false;
        }

        return $this->callable->getReturnType()->getName() === $type;
    }

    /**
     * Gets the return type of the callable.
     *
     * @throws InvalidArgumentException if the callable doesn't have
     *                                  an explicit return type.
     */
    public function getReturnType () : string {
        if (!$this->callable->hasReturnType()) {
            throw new InvalidArgumentException(
                "Callable doesn't have an explicit return type"
            );
        }

        return $this->callable->getReturnType()->getName();
    }

}
