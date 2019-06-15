<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Parser;

use Flyfinder\Finder;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\MountManager;
use LogicException;
use phpDocumentor\Dsn;
use phpDocumentor\Parser\FileSystemFactory;

final class FlySystemFactory implements FileSystemFactory
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
    public function create(Dsn $dsn): FilesystemInterface
    {
        $dsnId = hash('md5', (string) $dsn);

        try {
            $filesystem = $this->mountManager->getFilesystem($dsnId);
        } catch (LogicException $e) {
            if ($dsn->getScheme() === 'file') {
                $path = $dsn->getPath();
                $filesystem = new Filesystem(new Local($path, LOCK_EX, Local::SKIP_LINKS));
            } else {
                //This will be implemented as soon as the CloneRemoteGitToLocal adapter is finished
                throw new \InvalidArgumentException('http and https are not supported yet');
            }

            $this->mountManager->mountFilesystem($dsnId, $filesystem);
        }

        $filesystem->addPlugin(new Finder());

        return $filesystem;
    }
}
