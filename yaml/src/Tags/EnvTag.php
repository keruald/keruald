<?php

namespace Keruald\Yaml\Tags;

use Keruald\OmniTools\OS\Environment;

/**
 * Represents an environment variable
 */
class EnvTag extends Tag {

    public function getPrimaryTag () : string {
        return "tag:keruald.nasqueron.org,2025:env";
    }

    public function getPrivateTag () : string {
        return "env";
    }

    /**
     * @throws \InvalidArgumentException when the variable does not exist.
     */
    public function handle (mixed $data) : string {
        $key = (string)$data;

        return Environment::get($key);
    }

}
