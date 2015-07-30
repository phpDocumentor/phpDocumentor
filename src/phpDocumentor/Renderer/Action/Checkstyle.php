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

final class Checkstyle implements Action
{
    /** @var RenderPass */
    private $renderPass;

    /** @var Path|null */
    private $destination;

    public function __construct(RenderPass $renderPass, Path $destination = null)
    {
        $this->renderPass  = $renderPass;
        $this->destination = $destination;
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
            $destination ? new Path($destination) : null
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
     * @return Path|null
     */
    public function getDestination()
    {
        return $this->destination;
    }

    public function __toString()
    {
        return sprintf('Rendered checkstyle report at "%s"', $this->destination);
    }
}
