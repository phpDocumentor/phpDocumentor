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

use phpDocumentor\DomainModel\Template\Action;

/**
 * Business Logic for a single Action that will be executed by the Renderer based on the defined Actions in a Template.
 *
 * @see Action
 * @see Renderer
 * @see Template
 */
interface ActionHandler
{
    /**
     * Executes the activities that this Action represents.
     *
     * @param Action $action
     *
     * @return void
     */
    public function __invoke(Action $action);
}
