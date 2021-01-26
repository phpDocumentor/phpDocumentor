<?php

declare(strict_types=1);

namespace phpDocumentor\Configuration;

use ArrayAccess;
use InvalidArgumentException;
use phpDocumentor\Dsn;
use phpDocumentor\Path;
use function lcfirst;
use function property_exists;
use function str_replace;
use function ucwords;

/**
 * @implements ArrayAccess<String, mixed>
 */
final class ApiSpecification implements ArrayAccess
{
    /** @var array{dsn: Dsn, paths: array<Path>} */
    private $source;

    /** @var string */
    private $output;

    /** @var array{paths: array<Path>} */
    private $ignore;

    /** @var non-empty-list<string> */
    private $extensions;

    /** @var array<string> */
    private $visibility;

    /** @var string */
    private $defaultPackageName;

    /** @var bool */
    private $includeSource;

    /** @var array<string> */
    private $markers;

    /** @var array<string> */
    private $ignoreTags;

    /** @var array{dsn: Dsn, paths: list<string>}|null */
    private $examples;

    /** @var string */
    private $encoding;

    /** @var bool */
    private $validate;

    /**
     * @param array{dsn: Dsn, paths: array<Path>} $source
     * @param array{paths: array<Path>} $ignore
     * @param non-empty-list<string> $extensions
     * @param array<string> $visibility
     * @param array<string> $markers
     * @param array<string> $ignoreTags
     * @param array{dsn: Dsn, paths: list<string>}|null $examples
     */
    private function __construct(
        array $source,
        string $output,
        array $ignore,
        array $extensions,
        array $visibility,
        string $defaultPackageName,
        bool $includeSource,
        array $markers,
        array $ignoreTags,
        ?array $examples,
        string $encoding,
        bool $validate
    ) {
        $this->source = $source;
        $this->output = $output;
        $this->ignore = $ignore;
        $this->extensions = $extensions;
        $this->visibility = $visibility;
        $this->defaultPackageName = $defaultPackageName;
        $this->includeSource = $includeSource;
        $this->markers = $markers;
        $this->ignoreTags = $ignoreTags;
        $this->examples = $examples;
        $this->encoding = $encoding;
        $this->validate = $validate;
    }

    //phpcs:disable Generic.Files.LineLength.TooLong
    /**
     * @param array{ignore-tags: array<string>, extensions: non-empty-array<string>, markers: non-empty-array<string>, visibility: non-empty-array<string>, source: array{dsn: Dsn, paths: array}, ignore: array{paths: array}, encoding: string, output: string, default-package-name: string, examples: array{dsn: Dsn, paths: array}, include-source: bool, validate: bool} $api
     */
    //phpcs:enable Generic.Files.LineLength.TooLong
    public static function createFromArray(array $api) : self
    {
        return new self(
            $api['source'],
            $api['output'],
            $api['ignore'],
            $api['extensions'],
            $api['visibility'],
            $api['default-package-name'],
            $api['include-source'],
            $api['markers'],
            $api['ignore-tags'],
            $api['examples'],
            $api['encoding'],
            $api['validate']
        );
    }

    /**
     * @param array{dsn: Dsn, paths: array<Path>} $source
     */
    public function withSource(array $source) : self
    {
        $clone = clone $this;
        $clone->source = $source;

        return $clone;
    }

    /**
     * @param array{paths: non-empty-array<Path>} $ignore
     */
    public function setIgnore(array $ignore) : void
    {
        $this->ignore = $ignore;
    }

    /** @param string $offset */
    public function offsetExists($offset) : bool
    {
        $property = $this->normalizePropertyName($offset);

        return property_exists($this, $property);
    }

    /**
     * @param string $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        $property = $this->normalizePropertyName($offset);
        if (!property_exists($this, $property)) {
            throw new InvalidArgumentException('Invalid property ' . $property);
        }

        return $this->$property;
    }

    /**
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value) : void
    {
        $property = $this->normalizePropertyName($offset);
        if (!property_exists($this, $property)) {
            throw new InvalidArgumentException('Invalid property ' . $property);
        }

        $this->{$property} = $value;
    }

    /** @param string $offset */
    public function offsetUnset($offset) : void
    {
        $property = $this->normalizePropertyName($offset);
        if (!property_exists($this, $property)) {
            throw new InvalidArgumentException('Invalid property ' . $property);
        }

        $this->$property = null;
    }

    private function normalizePropertyName(string $offset) : string
    {
        return lcfirst(str_replace('-', '', ucwords($offset, '-')));
    }
}
