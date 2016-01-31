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

use phpDocumentor\DomainModel\Path;
use phpDocumentor\Renderer\Action;
use phpDocumentor\Renderer\RenderPass;
use phpDocumentor\Renderer\Template;
use Webmozart\Assert\Assert;

final class Graph implements Action
{
    /** @var Path */
    private $source;

    /** @var Path */
    private $destination;

    /** @var RenderPass */
    private $renderPass;

    /**
     * Factory method used to map a parameters array onto the constructor and properties for this Action.
     *
     * @param Template\Parameter[] $parameters
     *
     * @return static
     */
    public static function create(array $parameters)
    {
        Assert::keyExists($parameters, 'renderPass');
        Assert::keyExists($parameters, 'source');
        try {
            Assert::keyExists($parameters, 'destination');
        } catch (\InvalidArgumentException $e) {
            Assert::keyExists($parameters, 'artifact');
        }

        $destination = isset($parameters['artifact']) ? $parameters['artifact'] : $parameters['destination'];

        return new static(
            $parameters['renderPass']->getValue(),
            new Path($parameters['source']->getValue()),
            new Path($destination->getValue())
        );
    }

    /**
     * @return RenderPass
     */
    public function getRenderPass()
    {
        return $this->renderPass;
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
        return 'Generated the class diagram';
    }

    private function __construct(RenderPass $renderPass, Path $source, Path $destination)
    {
        $this->source      = $source;
        $this->destination = $destination;
        $this->renderPass  = $renderPass;
    }
}
