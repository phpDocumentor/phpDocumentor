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

namespace phpDocumentor\Parser;

use Flyfinder\Finder;
use InvalidArgumentException;
use League\Flysystem\Adapter\Local;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Filesystem;
use League\Flysystem\MountManager;
use LogicException;
use phpDocumentor\Dsn;
use Webmozart\Assert\Assert;

use function hash;
use function in_array;
use function sprintf;

use const LOCK_EX;
use const PHP_OS_FAMILY;

class FlySystemFactory implements FileSystemFactory
{
    /** @var MountManager */
    private $mountManager;

    public function __construct(MountManager $mountManager)
    {
        $this->mountManager = $mountManager;
    }

    /**
     * Returns a Filesystem instance based on the scheme of the provided Dsn
     */
    public function create(Dsn $dsn): Filesystem
    {
        $dsnId = hash('md5', (string) $dsn);

        try {
            $filesystem = $this->mountManager->getFilesystem($dsnId);
        } catch (LogicException $e) {
            $filesystem = new Filesystem($this->createAdapter($dsn));

            $this->mountManager->mountFilesystem($dsnId, $filesystem);
        }

        $filesystem->addPlugin(new Finder());

        Assert::isInstanceOf($filesystem, Filesystem::class);

        return $filesystem;
    }

    private function createAdapter(Dsn $dsn): AdapterInterface
    {
        if (!in_array($dsn->getScheme(), [null, 'file', 'vfs', 'phar'], true)) {
            throw new InvalidArgumentException(sprintf('"%s" is not a supported file system yet', $dsn->getScheme()));
        }

        return new Local(
            $this->formatDsn($dsn),
            $dsn->getScheme() !== 'vfs' ? LOCK_EX : 0, // VFS does not support locking
            Local::SKIP_LINKS
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
