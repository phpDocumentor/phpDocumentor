<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.4
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Application\Renderer\Template\Action;

use phpDocumentor\DomainModel\Renderer\Template\Action;
use phpDocumentor\DomainModel\Renderer\Renderer;
use phpDocumentor\DomainModel\Renderer\Template\ActionHandler;

class TwigHandler implements ActionHandler
{
    /** @var Renderer */
    private $renderer;

    public function __construct(Renderer $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * @param Action|Xml $action
     */
    public function __invoke(Action $action)
    {
        // TODO: Finish this
        // $this->renderer->render();
    }
}
