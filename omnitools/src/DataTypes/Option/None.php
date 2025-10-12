<?php

namespace Keruald\OmniTools\DataTypes\Option;

use InvalidArgumentException;

class None extends Option {

    public function isSome () : bool {
        return false;
    }

    public function isNone () : bool {
        return true;
    }

    public function getValue () : mixed {
        throw new InvalidArgumentException(<<<'EOD'
This option is a none, so it doesn't have a value.
You can check first with isSome() if this is a value.
EOD
        );
    }

    public function map (callable $callable) : Option {
        return $this;
    }

    public function orElse (mixed $default) : mixed {
        return $default;
    }
}
