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

namespace phpDocumentor\DomainModel\ReadModel;

use phpDocumentor\DomainModel\Parser\Documentation;

interface Mapper
{
    /**
     * Returns the data needed by the ViewFactory to create a new View.
     *
     * @param Definition $readModelDefinition
     * @param Documentation $documentation
     *
     * @return mixed
     */
    public function create(Definition $readModelDefinition, Documentation $documentation);
}
