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

namespace phpDocumentor\DomainModel\Renderer;

use phpDocumentor\DomainModel\DomainEvent;
use phpDocumentor\DomainModel\Renderer\Template\Action;

class RenderActionCompleted extends DomainEvent
{
    /** @var Action */
    private $action;

    public function __construct(Action $action)
    {
        $this->action = $action;
    }

    /**
     * @return Action
     */
    public function action()
    {
        return $this->action;
    }
}
