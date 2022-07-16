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

use Symfony\Component\Filesystem\Path as SymfonyPath;
use Webmozart\Assert\Assert;
use function sprintf;

/**
 * Value Object for paths.
 * This can be absolute or relative.
 */
final class Path
{
    private string $path;

    /**
     * Initializes the path.
     */
    public function __construct(string $path)
    {
        Assert::notEmpty(
            $path,
            sprintf('"%s" is not a valid path', $path)
        );

        // Canonicalizing paths will ensure they are all *NIX-like paths whose relative components are
        // converted into a regular representation. This normalization step will make it easier to work with.
        $this->path = SymfonyPath::canonicalize($path);

        // URL encoding will ensure we have a consistent format for the paths
        $this->path = implode('/', array_map('rawurlencode', explode('/', $this->path)));
    }

    /**
     * Verifies if another Path object has the same identity as this one.
     */
    public function equals(self $otherPath): bool
    {
        return $this->path === (string) $otherPath;
    }

    public function encoded(): string
    {
        return $this->path;
    }

    public function decoded(): string
    {
        return implode('/', array_map('rawurldecode', explode('/', $this->path)));
    }

    /**
     * Returns a string representation of the path.
     */
    public function __toString(): string
    {
        return $this->encoded();
    }

    /**
     * Returns whether the file path is an absolute path.
     *
     * @param string $file A file path
     */
    public static function isAbsolutePath(string $file): bool
    {
        return SymfonyPath::isAbsolute($file);
    }

    public static function dirname(Path $input): self
    {
        return new Path(SymfonyPath::getDirectory($input->decoded()));
    }
}
