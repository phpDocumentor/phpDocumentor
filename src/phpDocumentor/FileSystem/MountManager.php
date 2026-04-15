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

namespace phpDocumentor\FileSystem;

use Flyfinder\Specification\SpecificationInterface;

use function array_key_exists;
use function count;
use function explode;
use function sprintf;
use function strpos;

/**
 * This class manages filesystem layers within phpDocumentor.
 *
 * The purpose of this class is to be able to provide a read-only interface for template filesystems.
 * So when ever files are fetched from `data/templates` or directly from the template directory we are able
 * to get what we need. Flysystem v1 had support for this, but with the new abstraction we should not go there
 * anymore. This class will do something equal.
 */
final class MountManager implements FileSystem
{
    /** @param array<string, FileSystem> $filesystems */
    public function __construct(
        private array $filesystems,
        private string|null $defaultFileSystem = null,
    ) {
    }

    public function has(string $path): bool
    {
        ['scheme' => $fileSystem, 'path' => $foo] = $this->getSchemaAndPath($path);

        return $this->fileSystem($fileSystem)->has($foo);
    }

    public function lastModified(string $path): int
    {
        ['scheme' => $fileSystem, 'path' => $foo] = $this->getSchemaAndPath($path);

        return $this->fileSystem($fileSystem)->lastModified($foo);
    }

    public function readStream(string $path): mixed
    {
        ['scheme' => $fileSystem, 'path' => $foo] = $this->getSchemaAndPath($path);

        return $this->fileSystem($fileSystem)->readStream($foo);
    }

    public function read(string $path): string|false
    {
        ['scheme' => $fileSystem, 'path' => $foo] = $this->getSchemaAndPath($path);

        return $this->fileSystem($fileSystem)->read($foo);
    }

    public function put(string $path, string $contents): bool
    {
        ['scheme' => $fileSystem, 'path' => $foo] = $this->getSchemaAndPath($path);

        return $this->fileSystem($fileSystem)->put($foo, $contents);
    }

    public function listContents(string $directory = '', bool $recursive = false): array
    {
        ['scheme' => $fileSystem, 'path' => $path] = $this->getSchemaAndPath($directory);

        return $this->fileSystem($fileSystem)->listContents($path, $recursive);
    }

    public function find(SpecificationInterface $specification): iterable
    {
        if ($this->defaultFileSystem === null) {
            throw new FileNotFoundException('No default filesystem configured for find operation');
        }

        return $this->fileSystem($this->defaultFileSystem)->find($specification);
    }

    /** @return array{scheme: string, path: string} */
    private function getSchemaAndPath(string $path): array
    {
        if ($this->defaultFileSystem === null && strpos($path, '://') < 1) {
            throw new FileNotFoundException('No prefix detected in path: ' . $path);
        }

        $parts = explode('://', $path, 2);
        if (count($parts) === 2) {
            return ['scheme' => $parts[0], 'path' => $parts[1]];
        }

        return ['scheme' => $this->defaultFileSystem, 'path' => $path];
    }

    private function fileSystem(string $fileSystem): FileSystem
    {
        if (array_key_exists($fileSystem, $this->filesystems)) {
            return $this->filesystems[$fileSystem];
        }

        throw new FileNotFoundException(sprintf('Filesystem for schema %s not found', $fileSystem));
    }

    /** @param resource $resource */
    public function putStream(string $path, $resource): void
    {
        ['scheme' => $fileSystem, 'path' => $path] = $this->getSchemaAndPath($path);

        $this->fileSystem($fileSystem)->putStream($path, $resource);
    }

    public function isDirectory(string $path): bool
    {
        if ($this->has($path) === false) {
            return false;
        }

        ['scheme' => $fileSystem, 'path' => $path] = $this->getSchemaAndPath($path);

        return $this->fileSystem($fileSystem)->isDirectory($path);
    }
}
