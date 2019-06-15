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

use phpDocumentor\Reflection\Php\Factory;
use phpDocumentor\Reflection\Php\NodesFactory;

final class FileFactory
{
    /**
     * Symfony's !tagged helper expression returns an iterable object; but the FileFactory requires an array.
     * This means that we need to unpack the middlewares iterable first as a plain array in this factory.
     */
    public static function createInstance(NodesFactory $nodesFactory, iterable $middlewares): Factory\File
    {
        return new Factory\File($nodesFactory, iterator_to_array($middlewares));
    }
}
