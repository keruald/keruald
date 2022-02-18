<?php
declare(strict_types=1);

namespace Keruald\OmniTools\IO;

class File {

    /**
     * @var string
     */
    private $path;

    ///
    /// Constructors
    ///

    public function __construct (string $path) {
        $this->path = $path;
    }

    /**
     * @return static
     */
    public static function from (string $path) : self {
        return new static($path);
    }

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
    /// File properties methods
    ///

    public function exists () : bool {
        return file_exists($this->path);
    }

    public function isReadable () : bool {
        return is_readable($this->path);
    }

    public function getPathInfo () : array {
        return pathinfo($this->path);
    }

    public function getDirectory () : string {
        return pathinfo($this->path, PATHINFO_DIRNAME);
    }

    public function getFileName () : string {
        return pathinfo($this->path, PATHINFO_BASENAME);
    }

    public function getFileNameWithoutExtension () : string {
        return pathinfo($this->path, PATHINFO_FILENAME);
    }

    public function getExtension () : string {
        return pathinfo($this->path, PATHINFO_EXTENSION);
    }

}
