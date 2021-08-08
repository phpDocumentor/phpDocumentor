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

namespace phpDocumentor\Parser\Cache;

use phpDocumentor\Path;
use RuntimeException;
use Symfony\Contracts\Cache\CacheInterface;
use Webmozart\Assert\Assert;

use function error_get_last;
use function is_dir;
use function mkdir;
use function rtrim;
use function sprintf;

/**
 * Locates the cache folder and ensures that the Symfony Cache is routed to that folder.
 *
 * Contrary to the way Symfony regularly works, the cache folder can be provided by the configuration and should be
 * set at runtime.
 *
 * This class plays two roles in this process:
 *
 * 1. It exposes a Path that can dynamically change, this will allow other services not to depend on an actual path
 *    being passed in their constructor (which is cached by Symfony!) but this service so that their cache actions
 *    use the folder that is provided at runtime.
 * 2. It will initialize the Symfony Cache pools on runtime
 *
 * As long as all components that want to cache use this class; then that will ensure that cache is always written
 * to the same location.
 *
 * Caveat: this class does not change the location of the Symfony 'app' and 'system' cache pools by design; the Symfony
 * internal cache is still stored in folder dictated by {@see \phpDocumentor\Kernel::getCacheDir()}.
 */
class Locator
{
    /** @var ?Path */
    private $path;

    /** @var FilesystemAdapter */
    private $fileCache;

    /** @var FilesystemAdapter */
    private $descriptorCache;

    public function __construct(CacheInterface $files, CacheInterface $descriptors)
    {
        Assert::isInstanceOf($files, FilesystemAdapter::class);
        Assert::isInstanceOf($descriptors, FilesystemAdapter::class);

        $this->fileCache = $files;
        $this->descriptorCache = $descriptors;
    }

    public function providePath(Path $path): void
    {
        $this->path = $path;

        $this->fileCache->init('files', (string) $path);
        $this->descriptorCache->init('descriptors', (string) $path);
    }

    public function locate(string $namespace = ''): Path
    {
        $namespacePath = rtrim(sprintf('%s/%s', (string) $this->root(), $namespace), '/');

        if (!is_dir($namespacePath) && !@mkdir($namespacePath, 0777, true)) {
            $error = error_get_last();
            if ($error) {
                throw new RuntimeException(
                    sprintf(
                        'Received error "%s", while attempting to create directory "%s"',
                        $error['message'],
                        $namespacePath
                    )
                );
            }
        }

        return new Path($namespacePath);
    }

    private function root(): Path
    {
        if ($this->path === null) {
            throw new RuntimeException('Cache folder has not been set yet, please call `providePath` first');
        }

        return $this->path;
    }
}
