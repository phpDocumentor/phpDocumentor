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

namespace phpDocumentor\Renderer;

use Interop\Container\ContainerInterface;

/**
 * Service that attempts to locate an ActionHandler object based on the class name of a given Action.
 */
class ActionHandlerLocator
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Initializes this locator with the container so that it can retrieve the handlers for the given action.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Returns a handler for the given Action or null if none can be found.
     *
     * @param Action $action
     *
     * @return ActionHandler|null
     */
    public function locate(Action $action)
    {
        return $this->container->get(get_class($action) . 'Handler');
    }
}
