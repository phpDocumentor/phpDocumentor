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

final class Sourcecode implements Action
{
    /** @var RenderContext */
    private $renderContext;

    /** @var Path|null */
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
        Assert::keyExists($parameters, 'renderContext');
        try {
            Assert::keyExists($parameters, 'destination');
        } catch (\InvalidArgumentException $e) {
            Assert::keyExists($parameters, 'artifact');
        }

        // make this parameter BC-compatible with phpDocumentor 2
        $destination = isset($parameters['artifact'])
            ? $parameters['artifact']->getValue()
            : $parameters['destination']->getValue();

        return new static(
            $parameters['renderContext']->getValue(),
            $destination ? new Path($destination) : null
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
     * @return Path|null
     */
    public function getDestination()
    {
        return $this->destination;
    }

    public function __toString()
    {
        return sprintf('Added source code viewer at "%s"', $this->destination);
    }

    private function __construct(RenderContext $renderContext, Path $destination = null)
    {
        $this->renderContext  = $renderContext;
        $this->destination = $destination;
    }
}
