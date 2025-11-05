<?php

namespace Keruald\OmniTools\DataTypes\Result;

use Keruald\OmniTools\Reflection\CallableElement;

use InvalidArgumentException;
use Throwable;

class Err extends Result {
    private ?Throwable $error;

    const CB_TOO_MANY_ARGS = "The callback must take 0 or 1 argument.";

    public function __construct (Throwable $error = null) {
        $this->error = $error;
    }

    public function isOK () : false {
        return false;
    }

    public function isError () : true {
        return true;
    }

    public function getValue () : mixed {
        throw new InvalidArgumentException(<<<'EOD'
This result is an error, so it doesn't have a value.
You can check first with isOK() if this is a value.
Or if you want the error, use getError().
EOD
);
    }

    public function getError () : Throwable {
        return $this->error;
    }

    public function setError (Throwable $error) : void {
        $this->error = $error;
    }

    public function map (callable $callable) : self {
        return $this;
    }

    public function mapErr (callable $callable) : self {
        $error = $callable($this->error);

        return new self($error);
    }

    public function or (Result $default) : Result {
        return $default;
    }

    public function orElse (callable $callable) : Result {
        $argc = (new CallableElement($callable))->countArguments();

        return match($argc) {
            0 => $callable(),
            1 => $callable($this->error),

            default => throw new InvalidArgumentException(
                self::CB_TOO_MANY_ARGS, 0, $this->error
            ),
        };
    }

    public function getValueOr (mixed $default) : mixed {
        return $default;
    }

    public function getValueOrElse (callable $callable) : mixed {
        $argc = (new CallableElement($callable))->countArguments();

        return match($argc) {
            0 => $callable(),
            1 => $callable($this->error),

            default => throw new InvalidArgumentException(
                self::CB_TOO_MANY_ARGS, 0, $this->error
            ),
        };
    }
}
