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
     * @throws ReflectionException
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

}
