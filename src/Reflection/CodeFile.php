<?php

namespace Keruald\OmniTools\Reflection;

class CodeFile {

    /**
     * @var string
     */
    private $filename;

    ///
    /// Constructors
    ///

    public static function from (string $filename) : self {
        $instance = new self;
        $instance->filename = $filename;

        return $instance;
    }

    ///
    /// Getters and setters
    ///

    public function getFilename () : string {
        return $this->filename;
    }

    public function setFilename (string $filename) : self {
        $this->filename = $filename;

        return $this;
    }

    ///
    /// File properties methods
    ///

    public function exists () : bool {
        return file_exists($this->filename);
    }

    public function isReadable () : bool {
        return is_readable($this->filename);
    }

    ///
    /// Include methods
    ///

    public function tryInclude () : bool {
        if (!$this->canBeIncluded()) {
            return false;
        }

        include($this->filename);

        return true;
    }

    public function canBeIncluded () : bool {
        return $this->exists() &&$this->isReadable();
    }

}
