<?php

namespace Keruald\OmniTools\Reflection;

class CodeFile extends File {

    ///
    /// Include methods
    ///

    public function tryInclude () : bool {
        if (!$this->canBeIncluded()) {
            return false;
        }

        include($this->getFilename());

        return true;
    }

    public function canBeIncluded () : bool {
        return $this->exists() &&$this->isReadable();
    }

}
