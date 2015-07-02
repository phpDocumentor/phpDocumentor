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

namespace phpDocumentor\Application\Commands;

use Symfony\Component\Filesystem\Filesystem;

/**
 * Instructs the application to cache the parsed project at the given target location.
 */
final class CacheProject
{
    /** @var string the location where the cache should be stored */
    private $target;

    /**
     * Registers the given target location and creates a directory if it is doesn't exist yet.
     *
     * @param string $target
     */
    public function __construct($target)
    {
        $filesystem = new Filesystem();
        if (! $filesystem->isAbsolutePath($target)) {
            $target = getcwd() . DIRECTORY_SEPARATOR . $target;
        }
        if (! @file_exists($target) && ! @mkdir($target, 0777, true)) {
            throw new \InvalidArgumentException(
                sprintf('The cache directory "%s" does not exist and cannot be created', $target)
            );
        }

        $this->target = $target;
    }

    /**
     * Returns an absolute path pointing to the cache location.
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }
}
