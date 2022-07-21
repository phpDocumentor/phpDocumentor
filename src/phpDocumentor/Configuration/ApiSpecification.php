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

    /** @var int by default ignore internal visibility but show others */
    public const VISIBILITY_DEFAULT = 7;

    /** @var Source */
    private $source;

    /** @var string */
    private $output;

    /** @var array{
     *      hidden: bool,
     *      symlinks: bool,
     *      paths: list<string>
     *  }
     */
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

    /** @var Source|null */
    private $examples;

    /** @var string */
    private $encoding;

    /** @var bool */
    private $validate;

    /**
     * @param array{hidden: bool, symlinks: bool, paths: list<string>} $ignore
     * @param non-empty-list<string> $extensions
     * @param array<string> $visibility
     * @param array<string> $markers
     * @param array<string> $ignoreTags
     */
    private function __construct(
        Source $source,
        string $output,
        array $ignore,
        array $extensions,
        array $visibility,
        string $defaultPackageName,
        bool $includeSource,
        array $markers,
        array $ignoreTags,
        ?Source $examples,
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

    /**
     * @param ConfigurationApiMap $api
     */
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
                    array_map(static fn (string $path) => new Path($path), $api['examples']['paths'])
                )
                : null,
            $api['encoding'],
            $api['validate']
        );
    }

    public static function createDefault(): ApiSpecification
    {
        return new self(
            new Source(
                Dsn::createFromString('./'),
                [new Path('./src')]
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
            false,
            [],
            [],
            null,
            'utf8',
            false
        );
    }

    public function withSource(Source $source): self
    {
        $clone = clone $this;
        $clone->source = $source;

        return $clone;
    }

    /**
     * @param array{hidden: bool, symlinks: bool, paths: list<string>} $ignore
     */
    public function setIgnore(array $ignore): void
    {
        $this->ignore = $ignore;
    }

    /** @return string[] */
    public function getIgnoredTags(): array
    {
        return $this->ignoreTags;
    }

    public function calculateVisiblity(): int
    {
        $visibility = 0;

        foreach ($this->visibility as $item) {
            switch ($item) {
                case 'api':
                    $visibility |= self::VISIBILITY_API;
                    break;
                case 'public':
                    $visibility |= self::VISIBILITY_PUBLIC;
                    break;
                case 'protected':
                    $visibility |= self::VISIBILITY_PROTECTED;
                    break;
                case 'private':
                    $visibility |= self::VISIBILITY_PRIVATE;
                    break;
                case 'internal':
                    $visibility |= self::VISIBILITY_INTERNAL;
                    break;
                default:
                    throw new RuntimeException(
                        sprintf(
                            '%s is not a type of visibility, supported is: api, public, protected, private or internal',
                            $item
                        )
                    );
            }
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
}
