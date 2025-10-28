<?php

namespace Keruald\Yaml;

use Symfony\Component\Yaml\Tag\TaggedValue;
use Symfony\Component\Yaml\Yaml;

use Keruald\Yaml\Tags\Tag;

use InvalidArgumentException;

class Parser {

    /**
     * @var array<string, Tag>
     */
    private array $tags = [];

    ///
    /// Configure
    ///

    public function withTagClass (string $type) : self {
        $tag = new $type();

        return $this->withTag($tag);
    }

    public function withTag (Tag $tag) : self {
        $tag->register($this->tags);

        return $this;
    }

    ///
    /// Parse
    ///

    /**
     * @throws InvalidArgumentException if a tag is not defined at parser level
     */
    public function parse (string $expression) : mixed {
        $flags = Yaml::PARSE_CUSTOM_TAGS;
        $parsed = Yaml::parse($expression, $flags);

        return $this->resolve($parsed);
    }

    /**
     * @throws InvalidArgumentException if the file is not found
     * @throws InvalidArgumentException if a tag is not defined at parser level
     */
    public function parseFile (string $filename) : mixed {
        if (!file_exists($filename)) {
            throw new InvalidArgumentException("File not found: $filename");
        }

        $flags = Yaml::PARSE_CUSTOM_TAGS;
        $parsed = Yaml::parseFile($filename, $flags);

        return $this->resolve($parsed);
    }

    private function resolve (mixed $data) {
        if ($data instanceof TaggedValue) {
            $tag = $data->getTag();

            if (!array_key_exists($tag, $this->tags)) {
                throw new InvalidArgumentException("Tag not found: $tag");
            }

            $tagHandler = $this->tags[$tag];

            return $tagHandler->handle($data->getValue());
        }

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->resolve($value);
            }
        }

        return $data;
    }

}
