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

namespace phpDocumentor\DomainModel\Template;

use phpDocumentor\Renderer\Template;

/**
 * Represents the definition of an action as mentioned in the Template file(s).
 *
 * The actual execution of an Action is done by the associated ActionHandler which is in the same folder and has the
 * same name that ends with 'Handler'.
 *
 * @see ActionHandler
 */
interface Action
{
    /**
     * Factory method used to map a parameters array onto the constructor and properties for this Action.
     *
     * @param \phpDocumentor\DomainModel\Template\Parameter[] $parameters
     *
     * @return static
     */
    public static function create(array $parameters);

    public function __toString();
}
