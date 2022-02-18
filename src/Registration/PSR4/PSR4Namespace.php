<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Registration\PSR4;

use Keruald\OmniTools\IO\Directory;
use Keruald\OmniTools\IO\File;

class PSR4Namespace {

    public function __construct (
        public string $namespacePrefix,
        public string $baseDirectory,
    ) {
    }

    ///
    /// Auto-discovery
    ///

    /**
     * Discover classes in the namespace folder following PSR-4 convention,
     * directly at top-level, ignoring subdirectories.
     *
     * @see discoverRecursive
     * @return string[]
     */
    public function discover () : array {
        $files = (new Directory($this->baseDirectory))
            ->glob("*.php");

        return array_map(function (File $file) {
            return $this->namespacePrefix
                   . "\\" . $file->getFileNameWithoutExtension();
        }, $files);
    }

    /**
     * Discover classes in the namespace folder following PSR-4 convention,
     * including all subfolders.
     *
     * @return string[]
     */
    public function discoverRecursive () : array {
        $classes = $this->discover();

        $subDirectories = (new Directory($this->baseDirectory))
            ->getSubdirectories();

        foreach ($subDirectories as $dir) {
            $ns = new PSR4Namespace(
                $this->namespacePrefix . "\\" . $dir->getDirectoryName(),
                $dir->getPath(),
            );

            array_push($classes, ...$ns->discoverRecursive());
        }

        return $classes;
    }

    /**
     * Discover classes for a specific namespace in a specific folder,
     * following the PSR-4 convention, including all subfolders.
     *
     * @return string[]
     */
    public static function discoverAllClasses (
        string $namespacePrefix,
        string $baseDirectory
    ) : array {
        $ns = new PSR4Namespace($namespacePrefix, $baseDirectory);
        return $ns->discoverRecursive();
    }

}
