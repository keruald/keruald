<?php

namespace Keruald\Yaml\Tags;

abstract class Tag {

    public abstract function getPrimaryTag () : string;

    public abstract function getPrivateTag () : string;

    public abstract function handle (mixed $data) : mixed;

    public function register (&$tags) : void {
        $tags[$this->getPrimaryTag()] = $this;
        $tags[$this->getPrivateTag()] = $this;
    }

}
