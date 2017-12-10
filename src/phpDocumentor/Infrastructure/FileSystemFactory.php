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

use League\Flysystem\Filesystem;
use phpDocumentor\DomainModel\Dsn;

/**
 * Interface for FileSystem factories.
 */
interface FileSystemFactory
{
    /**
     * Returns a Filesystem instance based on the scheme of the provided Dsn.
     *
     * @param Dsn $dsn
     * @return Filesystem
     */
    public function create(Dsn $dsn);
}
