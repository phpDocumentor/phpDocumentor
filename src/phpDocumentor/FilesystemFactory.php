<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor;

use \LogicException;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\MountManager;
use \DI\Container;

/**
 * Class FilesystemFactory
 */
final class FilesystemFactory
{
    /** @var MountManager */
    private $mountManager;

    /** @var Container */
    private $container;

    /** @var Filesystem */
    private $filesystem;

    /**
     * @param MountManager $mountManager
     * @param Container $container
     */
    public function __construct(MountManager $mountManager, Container $container)
    {
        $this->mountManager = $mountManager;
        $this->container = $container;
    }

    /**
     * Returns a Filesystem instance based on the scheme of the provided Dsn
     *
     * @param Dsn $dsn
     * @return Filesystem
     */
    public function create(Dsn $dsn)
    {
        $dsnId = spl_object_hash($dsn);

        if (! $this->setFilesystemWhenCached($dsnId)) {
            if ($dsn->getScheme() === 'file') {

                $path = $dsn->getPath();
                $this->filesystem = new Filesystem(new Local(__DIR__. "/" . $path));

            } else {
                //This will be implemented as soon as the CloneRemoteGitToLocal adapter is finished
                throw new \InvalidArgumentException('http and https are not supported yet');
            }

            $this->mountManager->mountFilesystem($dsnId, $this->filesystem);
        }


        return $this->filesystem;
    }

    /**
     * If the requested filesystem is available in MountManager
     * set the filesystem property to this filesystem
     *
     * @param string $dsnId
     * @return bool
     */
    private function setFilesystemWhenCached($dsnId)
    {
        try {
            $this->filesystem = $this->mountManager->getFilesystem($dsnId);
            return true;
        } catch (LogicException $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }
}
