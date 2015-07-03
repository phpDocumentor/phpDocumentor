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

/**
 * Command used to load a project from the cache.
 */
final class LoadProjectFromCache
{
    /** @var string */
    private $source;

    /**
     * Provides the cache with a source location from where to load the project.
     *
     * @param string $source
     *
     * @throws \InvalidArgumentException if the given source does not exist
     * @throws \InvalidArgumentException if the given source is not a directory
     */
    public function __construct($source)
    {
        if (!file_exists($source) || !is_dir($source)) {
            throw new \InvalidArgumentException(
                'Invalid source location provided, a path to an existing folder was expected'
            );
        }

        $this->source = $source;
    }

    /**
     * Returns the source location where the project can be loaded from.
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }
}
