<?php

namespace Keruald\OmniTools\Reflection;

class Type {

    public static function getTypeOf ($v) : string {
        $type = gettype($v);

        if ($type === "object") {
            return get_class($v);
        }

        return $type;
    }

}
