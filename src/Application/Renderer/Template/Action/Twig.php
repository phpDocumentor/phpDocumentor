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

namespace phpDocumentor\Application\Renderer\Template\Action;

use phpDocumentor\DomainModel\Path;
use phpDocumentor\DomainModel\Renderer\Template\Action;
use phpDocumentor\DomainModel\Renderer\RenderContext;
use phpDocumentor\DomainModel\Renderer\Template;
use phpDocumentor\DomainModel\ReadModel\Definition;
use phpDocumentor\DomainModel\ReadModel\Type;
use Webmozart\Assert\Assert;

final class Twig implements Action
{
    /** @var RenderContext */
    private $renderContext;

    /** @var Path */
    private $view;

    /** @var string */
    private $query;

    /** @var Path|null */
    private $destination;

    /** @var Template */
    private $template;

    /** @var Definition */
    private $dataView;

    public function __construct(
        RenderContext $renderContext,
        Path $view,
        $query = '',
        Path $destination = null,
        Template $template = null,
        $dataView = null
    ) {
        Assert::string($query);

        if ($dataView === null) {
            $dataView = new Definition('project', new Type('php'));
        }

        $this->view        = $view;
        $this->query       = $query;
        $this->renderContext  = $renderContext;
        $this->destination = $destination;
        $this->template    = $template;
        $this->dataView    = $dataView;
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
            $parameters['renderContext']->getValue(),
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
     * @return RenderContext
     */
    public function getRenderContext()
    {
        return $this->renderContext;
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

    /**
     * @return Definition
     */
    public function getDataView()
    {
        return $this->dataView;
    }

    public function __toString()
    {
        return sprintf('Rendered view "%s" using Twig as "%s"', $this->view, $this->destination);
    }
}
