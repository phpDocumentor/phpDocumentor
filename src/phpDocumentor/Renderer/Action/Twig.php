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

namespace phpDocumentor\Renderer\Action;

use phpDocumentor\Path;
use phpDocumentor\Renderer\Action;
use phpDocumentor\Renderer\RenderPass;
use phpDocumentor\Renderer\Template;
use Webmozart\Assert\Assert;

final class Twig implements Action
{
    /** @var RenderPass */
    private $renderPass;

    /** @var Path */
    private $view;

    /** @var string */
    private $query;

    /** @var Path|null */
    private $destination;
    /**
     * @var Template
     */
    private $template;

    public function __construct(
        RenderPass $renderPass,
        Path $view,
        $query = '',
        Path $destination = null,
        Template $template = null
    ) {
        Assert::string($query);

        $this->view        = $view;
        $this->query       = $query;
        $this->renderPass  = $renderPass;
        $this->destination = $destination;
        $this->template    = $template;
    }

    /**
     * Factory method used to map a parameters array onto the constructor and properties for this Action.
     *
     * @param Template\Parameter[] $parameters
     *
     * @return static
     */
    public static function create(array $parameters)
    {
        // make this parameter BC-compatible with phpDocumentor 2
        $destination = isset($parameters['artifact'])
            ? $parameters['artifact']->getValue()
            : $parameters['destination']->getValue();

        return new static(
            $parameters['renderPass']->getValue(),
            new Path($parameters['source']->getValue()),
            $parameters['query']->getValue(),
            $destination ? new Path($destination) : null,
            isset($parameters['template']) ? $parameters['template']->getValue() : null
        );
    }

    /**
     * @return Path
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @return RenderPass
     */
    public function getRenderPass()
    {
        return $this->renderPass;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return Path|null
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @return Template
     */
    public function getTemplate()
    {
        return $this->template;
    }

    public function __toString()
    {
        return sprintf('Rendered view "%s" using Twig as "%s"', $this->view, $this->destination);
    }
}
