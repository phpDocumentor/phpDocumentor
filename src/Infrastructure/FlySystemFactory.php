<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2016 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Infrastructure;

use Flyfinder\Finder;
use \LogicException;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\MountManager;
use phpDocumentor\DomainModel\Dsn;

/**
 * Class FilesystemFactory
 */
final class FlySystemFactory implements FileSystemFactory
{
    /** @var MountManager */
    private $mountManager;

    /**
     * @param MountManager $mountManager
     */
    public function __construct(MountManager $mountManager)
    {
        $this->mountManager = $mountManager;
    }

    /**
     * Returns a Filesystem instance based on the scheme of the provided Dsn
     *
     * @param Dsn $dsn
     * @return Filesystem
     */
    public function create(Dsn $dsn)
    {
        $dsnId = hash('md5', (string)$dsn);

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
