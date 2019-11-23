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

use League\Flysystem\Filesystem;
use phpDocumentor\Dsn;

/**
 * Interface for FileSystem factories.
 */
interface FileSystemFactory
{
    /**
     * Returns a Filesystem instance based on the scheme of the provided Dsn.
     *
     * @return Filesystem
     */
    public function create(Dsn $dsn);
}
