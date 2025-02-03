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

use InvalidArgumentException;
use League\Flysystem\Adapter\Local;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Filesystem as FlySystemFilesystem;
use League\Flysystem\MountManager;
use LogicException;
use Webmozart\Assert\Assert;

use function hash;
use function in_array;
use function sprintf;

use const LOCK_EX;
use const PHP_OS_FAMILY;

class FlySystemFactory implements FileSystemFactory
{
    public function __construct(private readonly MountManager $mountManager)
    {
    }

    /**
     * Returns a Filesystem instance based on the scheme of the provided Dsn
     */
    public function create(Dsn $dsn): FileSystem
    {
        $dsnId = hash('md5', (string) $dsn);

        try {
            $filesystem = $this->mountManager->getFilesystem($dsnId);
        } catch (LogicException) {
            $filesystem = new FlySystemFilesystem($this->createAdapter($dsn));

            $this->mountManager->mountFilesystem($dsnId, $filesystem);
        }

        Assert::isInstanceOf($filesystem, FlySystemFilesystem::class);

        return FlySystemAdapter::createFromFileSystem($filesystem);
    }

    private function createAdapter(Dsn $dsn): AdapterInterface
    {
        if (! in_array($dsn->getScheme(), [null, 'file', 'vfs', 'phar'], true)) {
            throw new InvalidArgumentException(sprintf('"%s" is not a supported file system yet', $dsn->getScheme()));
        }

        return new Local(
            $this->formatDsn($dsn),
            $dsn->getScheme() !== 'vfs' ? LOCK_EX : 0, // VFS does not support locking
            Local::SKIP_LINKS,
        );
    }

    /**
     * Removes file:/// scheme from the dsn when needed.
     *
     * The local adapter of flysystem cannot handle the file:/// on all windows
     * platforms. At the moment it is unsure why this is exactly happening this way
     * but it seems that php on windows 10 is not able to handle streams properly while
     * windows server is able to do this.
     *
     * Github actions will NOT reproduce this behavior since they are running a server edition of windows.
     */
    private function formatDsn(Dsn $dsn): string
    {
        if (PHP_OS_FAMILY === 'Windows' && $dsn->isWindowsLocalPath() && $dsn->getScheme() === 'file') {
            return (string) $dsn->getPath();
        }

        return (string) $dsn;
    }
}
