#!/usr/bin/env php
<?php

#   -------------------------------------------------------------
#   Generate composer.json from packages metadata
#   - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
#   Project:        Keruald
#   License:        BSD-2-Clause
#   Dependencies:   symfony/yaml, keruald/omnitools
#   -------------------------------------------------------------

use Keruald\OmniTools\Collections\Vector;
use Keruald\OmniTools\Collections\HashMap;
use Symfony\Component\Yaml\Parser as YamlParser;

require_once __DIR__ . "/../../vendor/autoload.php";

#   -------------------------------------------------------------
#   Helper methods to build elements from metadata
#   - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

function getAutoload(array $packages_namespaces) : array {
    return [
        "psr-4" => HashMap::from($packages_namespaces)
                          ->flatMap(fn($package, $ns) => [
                              $ns . '\\'        => $package . "/src/",
                              $ns . '\\Tests\\' => $package . "/tests/",
                          ])
                          ->toArray()
    ];
}

function getReplace(array $packages) : array {
    return HashMap::from($packages)
                  ->mapValuesAndKeys(
                      fn($v) => ["keruald/$v", getPackageVersion($v)]
                  )
                  ->toArray();
}

function getPackageVersion (string $package) : string {
    $tags = PackagesTags::load();

    return $tags->packages[$package] ?? "self.version";
}

class PackagesTags {

    ///
    /// Singleton pattern
    ///

    private static ?self $instance = null;

    public static function load () : self {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    ///
    /// Properties
    ///

    public HashMap $packages;

    ///
    /// Constructor
    ///

    public function __construct () {
        $this->packages = self::getPackagesLastTags();
    }

    ///
    /// Read metadata from Git repository
    ///

    private static function getPackagesLastTags () : HashMap {
        $map = new HashMap;

        $tags = self::getRepositoryTags();
        foreach ($tags as $tag) {
            [$package, $version] = explode("/", $tag);

            if (!$map->has($package)) {
                $map->set($package, $version);
                continue;
            }

            if (version_compare($map[$package], $version) == -1) {
                $map->set($package, $version);
            }
        }

        return $map;
    }

    private static function getRepositoryTags () : Vector {
        exec("git tag", $tags);

        return Vector::from($tags)
                     ->filter(fn($tag) => str_contains($tag, "/"));
    }

}

#   -------------------------------------------------------------
#   Template
#   - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

function run() : never {
    $metadata = (new YamlParser())->parseFile("metadata.yml");
    $flags = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;

    echo json_encode(getTemplate($metadata), $flags), "\n";

    exit(0);
}

function getTemplate(array $metadata) : array {
    return [
        "name" => "keruald/keruald",
        "type" => "library",
        "description" => "Modular libraries to build frameworks and applications",
        "keywords" => [
            "framework",
            "keruald",
        ],
        "license" => "BSD-2-Clause",
        "homepage" => "https://keruald.nasqueron.org",
        "authors" => [
            [
                "name" => "Sébastien Santoro",
                "email" => "dereckson@espace-win.org",
            ],
            [
                "name" => "Keruald contributors",
            ],
        ],
        "provide" => [
            "psr/simple-cache-implementation" => "1.0|2.0|3.0",
        ],
        "require" => [
            "psr/simple-cache" => "^1.0|^2.0|^3.0",
            "ext-intl" => "*",
        ],
        "require-dev" => [
            "ext-mbstring" => "*",
            "ext-mysqli" => "*",
            "ext-xmlwriter" => "*",
            "nasqueron/codestyle" => "^0.1.2",
            "phan/phan" => "^5.3.1",
            "phpunit/phpunit" => "^12.4",
            "symfony/yaml" => "^6.0.3",
            "squizlabs/php_codesniffer" => "^4.0.0",
        ],
        "suggest" => [
            "ext-memcached" => "*",
            "ext-redis" => "*",
        ],
        "replace" => getReplace($metadata["packages"]),
        "autoload" => getAutoload($metadata["packages_namespaces"]),
        "scripts" => [
            "lint-src" => "find */src -type f -name '*.php' | xargs -n1 php -l",
            "lint-tests" => "find */tests -type f -name '*.php' | xargs -n1 php -l",
            "test" => "vendor/bin/phpunit",
        ],
        "minimum-stability" => "dev",
    ];
}

run();
