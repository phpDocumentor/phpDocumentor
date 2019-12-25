<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
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
use const LOCK_EX;
use function assert;
use function hash;
use function in_array;

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
    public function create(Dsn $dsn) : Filesystem
    {
        $dsnId = hash('md5', (string) $dsn);

        try {
            $filesystem = $this->mountManager->getFilesystem($dsnId);
        } catch (LogicException $e) {
            $filesystem = new Filesystem($this->createAdapter($dsn));

            $this->mountManager->mountFilesystem($dsnId, $filesystem);
        }

        $filesystem->addPlugin(new Finder());

        assert($filesystem instanceof Filesystem);

        return $filesystem;
    }

    private function createAdapter(Dsn $dsn) : AdapterInterface
    {
        if (!in_array($dsn->getScheme(), [null, 'file', 'vfs', 'phar'])) {
            throw new InvalidArgumentException('http and https are not supported yet');
        }

        try {
            $root = (string) $dsn;
        } catch (InvalidArgumentException $e) {
            throw new InvalidArgumentException(
                'Failed to determine the root path for the given DSN, received: ' . (string) $dsn,
                0,
                $e
            );
        }

        return new Local(
            $root,
            $dsn->getScheme() !== 'vfs' ? LOCK_EX : 0, // VFS does not support locking
            Local::SKIP_LINKS
        );
    }
}
