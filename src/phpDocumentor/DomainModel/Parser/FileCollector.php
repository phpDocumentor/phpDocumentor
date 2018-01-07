<?php
/**
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @copyright 2010-2018 Mike van Riel<mike@phpdoc.org>
 *  @license   http://www.opensource.org/licenses/mit-license.php MIT
 *  @link      http://phpdoc.org
 */

namespace phpDocumentor\DomainModel\Parser;

use phpDocumentor\DomainModel\Dsn;
use phpDocumentor\Reflection\File;

interface FileCollector
{
    /**
     * @param Dsn $dsn dsn of source.
     * @param string[] $paths
     * @param array $ignore array containing keys 'paths' and 'hidden'
     * @param string[] $extensions
     * @return File[]
     */
    public function getFiles(Dsn $dsn, array $paths, array $ignore, array $extensions): array;
}
