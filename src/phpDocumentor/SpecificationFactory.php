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

use Flyfinder\Specification\SpecificationInterface;

/**
 * Interface for Specifications used to filter the FileSystem.
 */
interface SpecificationFactory
{
    /**
     * Creates a SpecificationInterface object based on the ignore and extension parameters.
     *
     * @param array $paths
     * @param array $ignore
     * @param array $extensions
     * @return SpecificationInterface
     */
    public function create(array $paths, array $ignore, array $extensions);
}
