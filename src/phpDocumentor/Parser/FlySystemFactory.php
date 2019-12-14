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
use League\Flysystem\FilesystemInterface;
use League\Flysystem\MountManager;
use LogicException;
use phpDocumentor\Dsn;
use const LOCK_EX;
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
    public function create(Dsn $dsn) : FilesystemInterface
    {
        $dsnId = hash('md5', (string) $dsn);

        try {
            $filesystem = $this->mountManager->getFilesystem($dsnId);
        } catch (LogicException $e) {
            $filesystem = new Filesystem($this->createAdapter($dsn));

            $this->mountManager->mountFilesystem($dsnId, $filesystem);
        }

        $filesystem->addPlugin(new Finder());

        return $filesystem;
    }

    private function createAdapter(Dsn $dsn) : AdapterInterface
    {
        if (!in_array($dsn->getScheme(), ['file', 'vfs'])) {
            throw new InvalidArgumentException('http and https are not supported yet');
        }

        try {
            // FlySystem does not like file://.; so for local files we strip the path; but for vfs we want to keep the
            // scheme
            $root = $dsn->getScheme() === 'file' ? (string) $dsn->getPath() : (string) $dsn;
        } catch (InvalidArgumentException $e) {
            throw new InvalidArgumentException(
                'Failed to determine the root path for the given DSN, received: ' . (string) $dsn,
                0,
                $e
            );
        }

        return new Local(
            $root,
            LOCK_EX,
            Local::SKIP_LINKS
        );
    }
}
