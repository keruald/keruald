#!/usr/bin/env php
<?php

#   -------------------------------------------------------------
#   Generate composer.json from packages metadata
#   - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
#   Project:        Keruald
#   License:        BSD-2-Clause
#   Dependencies:   symfony/yaml, keruald/omnitools
#   -------------------------------------------------------------

use Symfony\Component\Yaml\Parser as YamlParser;
use Keruald\OmniTools\Collections\HashMap;

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
                  ->mapValuesAndKeys(fn($v) => ["keruald/$v", "self.version"])
                  ->toArray();
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
        "require" => [
            "ext-intl" => "*",
        ],
        "require-dev" => [
            "ext-mbstring" => "*",
            "ext-mysqli" => "*",
            "ext-xmlwriter" => "*",
            "nasqueron/codestyle" => "^0.0.1",
            "phan/phan" => "^5.3.1",
            "phpunit/phpunit" => "^10.2",
            "symfony/yaml" => "^6.0.3",
            "squizlabs/php_codesniffer" => "^3.6",
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
