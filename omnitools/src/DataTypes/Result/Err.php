<?php

namespace Keruald\OmniTools\DataTypes\Result;

use Exception;
use InvalidArgumentException;
use Throwable;

class Err extends Result {
    private ?Throwable $error;

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

    public function orElse (mixed $default) : mixed {
        return $default;
    }
}
