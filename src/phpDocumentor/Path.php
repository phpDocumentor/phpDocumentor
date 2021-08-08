<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor;

use Webmozart\Assert\Assert;

use function array_pop;
use function ctype_alpha;
use function explode;
use function implode;
use function parse_url;
use function sprintf;
use function strlen;
use function strspn;

use const PHP_URL_SCHEME;

/**
 * Value Object for paths.
 * This can be absolute or relative.
 */
final class Path
{
    /** @var string */
    private $path;

    /**
     * Initializes the path.
     */
    public function __construct(string $path)
    {
        Assert::notEmpty(
            $path,
            sprintf('"%s" is not a valid path', $path)
        );

        $this->path = $path;
    }

    /**
     * Verifies if another Path object has the same identity as this one.
     */
    public function equals(self $otherPath): bool
    {
        return $this->path === (string) $otherPath;
    }

    /**
     * returns a string representation of the path.
     */
    public function __toString(): string
    {
        return $this->path;
    }

    /**
     * Returns whether the file path is an absolute path.
     *
     * @param string $file A file path
     */
    public static function isAbsolutePath(string $file): bool
    {
        return strspn($file, '/\\', 0, 1)
            || (strlen($file) > 3 && ctype_alpha($file[0])
                && $file[1] === ':'
                && strspn($file, '/\\', 2, 1)
            )
            || parse_url($file, PHP_URL_SCHEME) !== null;
    }

    public static function dirname(Path $input): self
    {
        $parts = explode('/', (string) $input);
        array_pop($parts);

        return new self(implode('/', $parts));
    }
}
