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
use Webmozart\Assert\Assert;

final class CopyFile implements Action
{
    /** @var RenderContext */
    private $renderContext;

    /** @var Path */
    private $source;

    /** @var Path */
    private $destination;

    /**
     * Factory method used to map a parameters array onto the constructor and properties for this Action.
     *
     * @param Template\Parameter[] $parameters
     *
     * @return static
     */
    public static function create(array $parameters)
    {
        Assert::allIsInstanceOf($parameters, Template\Parameter::class);
        Assert::keyExists($parameters, 'renderContext');
        Assert::isInstanceOf($parameters['renderContext']->getValue(), RenderContext::class);
        Assert::keyExists($parameters, 'source');
        Assert::string($parameters['source']->getValue());
        Assert::keyExists($parameters, 'destination');
        Assert::string($parameters['destination']->getValue());

        return new static(
            $parameters['renderContext']->getValue(),
            new Path($parameters['source']->getValue()),
            new Path($parameters['destination']->getValue())
        );
    }

    /**
     * @return RenderContext
     */
    public function getRenderContext()
    {
        return $this->renderContext;
    }

    /**
     * @return Path
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return Path
     */
    public function getDestination()
    {
        return $this->destination;
    }

    public function __toString()
    {
        return sprintf('Copied file %s to %s', $this->source, $this->destination);
    }

    private function __construct(RenderContext $renderContext, Path $source, Path $destination)
    {
        $this->renderContext = $renderContext;
        $this->source        = $source;
        $this->destination   = $destination;
    }
}
