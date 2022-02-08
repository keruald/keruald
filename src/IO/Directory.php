<?php

namespace Keruald\OmniTools\IO;

class Directory {

    ///
    /// Constructors
    ///

    public function __construct (
        private string $path,
    ) {}

    ///
    /// Getters and setters
    ///

    public function getPath () : string {
        return $this->path;
    }

    public function setPath (string $path) : self {
        $this->path = $path;

        return $this;
    }

    ///
    /// Directory properties methods
    ///

    public function exists () : bool {
        return is_dir($this->path);
    }

    public function isReadable () : bool {
        return is_readable($this->path);
    }

    public function isWritable () : bool {
        return is_writable($this->path);
    }

    /**
     * @return array<string, string>
     */
    public function getPathInfo () : array {
        return pathinfo($this->path);
    }

    public function getParentDirectory () : string {
        return pathinfo($this->path, PATHINFO_DIRNAME);
    }

    public function getDirectoryName () : string {
        return pathinfo($this->path, PATHINFO_BASENAME);
    }

    ///
    /// Search files
    ///

    /**
     * Gets files in the directory matching a specific pattern,
     * using the PHP glob function.
     *
     * @return File[]
     */
    public function glob (string $pattern) : array {
        return array_map(
            function ($file) {
                return new File($file);
            }, glob("$this->path/$pattern")
        );
    }

    /**
     * @return Directory[]
     */
    public function getSubdirectories () : array {
        return array_map(
            function ($dir) {
                return new Directory($dir);
            }, glob("$this->path/*", GLOB_ONLYDIR)
        );
    }

}
