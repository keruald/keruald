<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Registration\PSR4;

use Keruald\OmniTools\Reflection\CodeFile;

final class Autoloader {

    ///
    /// Private members
    ///

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var string The base path where files for this namespace are located
     */
    private $path;

    ///
    /// Constructor
    ///

    public function __construct (string $namespace, string $path) {
        $this->namespace = $namespace;
        $this->path = $path;
    }

    ///
    /// Public methods
    ///

    public function getSolver (string $class) : Solver {
        return new Solver($this->namespace, $this->path, $class);
    }

    public function register () : void {
        spl_autoload_register(function ($class) {
            $solver = $this->getSolver($class);

            if (!$solver->canResolve()) {
                return;
            }

            CodeFile::from($solver->resolve())->tryInclude();
        });
    }

}
