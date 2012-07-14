<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin;

/**
 * Base class for plugin event listeners.
 *
 * @author  Mike van Riel <mike.vanriel@naenius.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    http://phpdoc.org
 */
abstract class ListenerAbstract extends PluginAbstract
{
    /** @var PluginAbstract */
    protected $plugin = null;

    /**
     * Registers the event dispatcher and configuration, calls the configure
     * method and connects the hooks of this listener to the event dispatcher.
     *
     * @param PluginAbstract $plugin Plugin object to register this listener on.
     */
    public function __construct($plugin)
    {
        $this->plugin = $plugin;

        parent::__construct(
            $plugin->getEventDispatcher(), $plugin->getConfiguration(),
            $plugin->getTranslator()
        );

        $this->configure();

        $this->connectHooksToDispatcher();
    }

    /**
     * Hook method to allow some additional processing before the hooks are
     * connected to the dispatcher.
     *
     * This method can be used to manually hook events to the dispatcher based
     * instead of using the @phpdoc-event tag.
     *
     * @see \phpDocumentor\Plugin\Core\Listener::configure() for an example of use.
     *
     * @return void
     */
    protected function configure()
    {
    }

    /**
     * Scans this class for any method containing the @phpdoc-event tag and
     * connects it to the event dispatcher.
     *
     * The @phpdoc-event tag has as description the name of the event to
     * connect to. When encountered will that event be linked to the associated
     * method.
     *
     * It is thus important that such a method has a single argument $event of
     * type sfEvent. This contains the arguments that were dispatched.
     *
     * @return void
     */
    protected function connectHooksToDispatcher()
    {
        $refl = new \ReflectionObject($this);

        // connect all events of the each method to the event_dispatcher
        /** @var \ReflectionMethod $method */
        foreach ($refl->getMethods() as $method) {
            if (!$method->getDocComment()) {
                continue;
            }

            $docblock = new \phpDocumentor\Reflection\DocBlock(
                $method->getDocComment()
            );

            /** @var \phpDocumentor\Reflection\DocBlock\Tag $event */
            foreach ($docblock->getTagsByName('phpdoc-event') as $event) {
                $this->event_dispatcher->addListener(
                    $event->getDescription(),
                    array($this, $method->getName())
                );
            }
        }
    }
}
