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

namespace phpDocumentor\Configuration;

use ArrayAccess;
use BadMethodCallException;
use OutOfBoundsException;
use phpDocumentor\Dsn;
use phpDocumentor\Path;
use ReturnTypeWillChange;

use function array_map;
use function in_array;
use function ltrim;
use function rtrim;
use function sprintf;
use function strpos;
use function substr;

/** @implements ArrayAccess<string, Path[]|Dsn> */
final class Source implements ArrayAccess
{
    /** @var Dsn */
    private $dsn;

    /** @var Path[] */
    private $paths;

    /** @param Path[] $paths */
    public function __construct(Dsn $dsn, array $paths)
    {
        $this->dsn = $dsn;
        $this->paths = $paths;
    }

    public function withDsn(Dsn $dsn): Source
    {
        $self = clone $this;
        $self->dsn = $dsn;

        return $self;
    }

    /** @param Path[] $paths */
    public function withPaths(array $paths): Source
    {
        $self = clone $this;
        $self->paths = $paths;

        return $self;
    }

    public function dsn(): Dsn
    {
        return $this->dsn;
    }

    /** @return Path[] */
    public function paths(): array
    {
        return $this->paths;
    }

    /** @return string[] */
    public function globPatterns(): array
    {
        return array_map(
            function (Path $path): string {
                return $this->pathToGlobPattern((string) $path);
            },
            $this->paths
        );
    }

    private function normalizePath(string $path): string
    {
        if (strpos($path, '.') === 0) {
            $path = ltrim($path, '.');
        }

        if (strpos($path, '/') !== 0) {
            $path = '/' . $path;
        }

        return rtrim($path, '/');
    }

    private function pathToGlobPattern(string $path): string
    {
        $path = $this->normalizePath($path);

        if (substr($path, -1) !== '*' && strpos($path, '.') === false) {
            $path .= '/**/*';
        }

        return $path;
    }

    /** @param string $offset */
    #[ReturnTypeWillChange]
    public function offsetExists($offset): bool
    {
        return in_array($offset, ['dsn', 'paths']);
    }

    /**
     * @param string $offset
     *
     * @return Path[]|Dsn
     */
    #[ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        switch ($offset) {
            case 'dsn':
                return $this->dsn;

            case 'paths':
                return $this->paths;

            default:
                throw new OutOfBoundsException(sprintf('Offset %s does not exist', $offset));
        }
    }

    /**
     * @param string $offset
     * @param mixed $value
     */
    #[ReturnTypeWillChange]
    public function offsetSet($offset, $value): void
    {
        throw new BadMethodCallException('Cannot set offset of ' . self::class);
    }

    /** @param string $offset */
    #[ReturnTypeWillChange]
    public function offsetUnset($offset): void
    {
        throw new BadMethodCallException('Cannot unset offset of ' . self::class);
    }
}
