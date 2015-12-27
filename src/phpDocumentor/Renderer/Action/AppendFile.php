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

final class AppendFile implements Action
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
        Assert::keyExists($parameters, 'destination');

        return new static(
            $parameters['renderPass']->getValue(),
            new Path($parameters['source']->getValue()),
            new Path($parameters['destination']->getValue())
        );
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

    /**
     * @return RenderPass
     */
    public function getRenderPass()
    {
        return $this->renderPass;
    }

    public function __toString()
    {
        return sprintf('Appended file %s onto %s', $this->source, $this->destination);
    }

    private function __construct(RenderPass $renderPass, Path $source, Path $destination)
    {
        $this->renderPass  = $renderPass;
        $this->source      = $source;
        $this->destination = $destination;
    }
}
