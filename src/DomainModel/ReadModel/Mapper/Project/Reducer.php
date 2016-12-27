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

namespace phpDocumentor\DomainModel\ReadModel\Mapper\Project;

/**
 * Interface that reducers for the project mapper should implement.
 */
interface Reducer
{
    /**
     * Converts the element to an array.
     *
     * @param Interpret $command
     *
     * @return array
     */
    public function __invoke(Interpret $command, $state);
}
