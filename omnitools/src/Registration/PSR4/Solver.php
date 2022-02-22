<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Registration\PSR4;

final class Solver {

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var string The base path for the namespace
     */
    private $path;

    /**
     * @var string The fully qualified class name
     */
    private $class;

    ///
    /// Constructors
    ///

    public function __construct (string $namespace, string $path, string $class) {
        $this->namespace = $namespace;
        $this->path = $path;
        $this->class = $class;
    }

    ///
    /// Resolve methods
    ///

    public function resolve () : string {
        return $this->path
             . '/'
             . $this->getRelativePath();
    }

    public function canResolve () : bool {
        return str_starts_with($this->class, $this->namespace);
    }

    public static function getPathFor (string $name) : string {
        return str_replace("\\", "/", $name) . '.php';
    }

    ///
    /// Helper methods
    ///

    private function getRelativePath () : string {
        return self::getPathFor($this->getLocalClassName());
    }

    private function getLocalClassName () : string {
        $len = strlen($this->namespace);
        return substr($this->class, $len);
    }

}
