<?php

declare(strict_types=1);

namespace phpDocumentor\Configuration;

use ArrayAccess;
use phpDocumentor\Configuration\Definition\Version3;
use phpDocumentor\Dsn;
use phpDocumentor\Path;
use RuntimeException;
use Webmozart\Assert\Assert;

use function array_map;
use function sprintf;
use function strtolower;

/**
 * @psalm-import-type ConfigurationApiMap from Version3
 * @implements ArrayAccess<String, mixed>
 */
final class ApiSpecification implements ArrayAccess
{
    use LegacyArrayAccess;

    public const VISIBILITY_PUBLIC = 1;
    public const VISIBILITY_PROTECTED = 2;
    public const VISIBILITY_PRIVATE = 4;
    public const VISIBILITY_INTERNAL = 8;
    public const VISIBILITY_API = 16;

    public const VISIBILITY_DEFAULT = 7;

    /**
     * @param array{hidden: bool, symlinks: bool, paths: list<string>} $ignore
     * @param non-empty-list<string> $extensions
     * @param array<string> $visibility
     * @param array<string> $markers
     * @param array<string> $ignoreTags
     */
    private function __construct(
        private Source $source,
        private string $output,
        private array $ignore,
        private array $extensions,
        private array $visibility,
        private string $defaultPackageName,
        private bool|null $includeSource,
        private array $markers,
        private array $ignoreTags,
        private Source|null $examples,
        private string $encoding,
        private bool $validate,
        private bool $ignorePackages,
    ) {
    }

    /** @param ConfigurationApiMap $api */
    public static function createFromArray(array $api): self
    {
        $sourcePaths = $api['source']['paths'];
        Assert::allIsInstanceOf($sourcePaths, Path::class);

        $sourceDsn = $api['source']['dsn'];
        Assert::isInstanceOf($sourceDsn, Dsn::class);

        return new self(
            new Source($sourceDsn, $sourcePaths),
            $api['output'],
            $api['ignore'],
            $api['extensions'],
            $api['visibility'],
            $api['default-package-name'],
            $api['include-source'],
            $api['markers'],
            $api['ignore-tags'],
            isset($api['examples'])
                ? new Source(
                    Dsn::createFromString($api['examples']['dsn']),
                    array_map(static fn (string $path) => new Path($path), $api['examples']['paths']),
                )
                : null,
            $api['encoding'],
            $api['validate'],
            $api['ignore-packages'],
        );
    }

    public static function createDefault(): ApiSpecification
    {
        return new self(
            new Source(
                Dsn::createFromString('./'),
                [new Path('./src')],
            ),
            './api',
            [
                'hidden' => true,
                'symlinks' => true,
                'paths' => [],
            ],
            ['php'],
            [],
            '',
            null,
            [],
            [],
            null,
            'utf8',
            false,
            false,
        );
    }

    public function withSource(Source $source): self
    {
        $clone = clone $this;
        $clone->source = $source;

        return $clone;
    }

    /** @param array{hidden: bool, symlinks: bool, paths: list<string>} $ignore */
    public function setIgnore(array $ignore): void
    {
        $this->ignore = $ignore;
    }

    /** @return string[] */
    public function getIgnoredTags(): array
    {
        $tags =  $this->ignoreTags;
        if ($this->ignorePackages) {
            $tags[] = 'package';
        }

        return $tags;
    }

    public function calculateVisiblity(): int
    {
        $visibility = 0;

        foreach ($this->visibility as $item) {
            match (strtolower($item)) {
                'api' => $visibility |= self::VISIBILITY_API,
                'public' => $visibility |= self::VISIBILITY_PUBLIC,
                'protected' => $visibility |= self::VISIBILITY_PROTECTED,
                'private' => $visibility |= self::VISIBILITY_PRIVATE,
                'internal' => $visibility |= self::VISIBILITY_INTERNAL,
                default => throw new RuntimeException(
                    sprintf(
                        '%s is not a type of visibility, supported is: api, public, protected, private or internal',
                        $item,
                    ),
                ),
            };
        }

        if ($visibility === self::VISIBILITY_INTERNAL) {
            $visibility |= self::VISIBILITY_DEFAULT;
        }

        return $visibility;
    }

    /**
     * Checks whether the Project supports the given visibility.
     *
     * @see Settings for a list of the available VISIBILITY_* constants.
     *
     * @param int $visibility One of the VISIBILITY_* constants of the Settings class.
     */
    public function isVisibilityAllowed(int $visibility): bool
    {
        $visibilityAllowed = $this->calculateVisiblity();

        return (bool) ($visibilityAllowed & $visibility);
    }

    public function source(): Source
    {
        return $this->source;
    }

    public function ignorePackages(): bool
    {
        return $this->ignorePackages;
    }
}
