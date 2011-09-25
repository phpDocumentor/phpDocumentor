<?php
abstract class DocBlox_Plugin_ListenerAbstract extends DocBlox_Plugin_Abstract
{
    protected $plugin = null;

    /**
     * @param DocBlox_Plugin_Abstract $plugin
     */
    public function __construct($plugin)
    {
        $this->plugin = $plugin;

        parent::__construct(
            $plugin->getEventDispatcher(), $plugin->getConfiguration()
        );

        $this->configure();

        $this->connectHooksToDispatcher();
    }

    protected function configure()
    {
    }

    protected function connectHooksToDispatcher()
    {
        $refl = new ReflectionObject($this);

        // connect all events of the each method to the event_dispatcher
        /** @var ReflectionMethod $method */
        foreach ($refl->getMethods() as $method) {
            if (!$method->getDocComment()) {
                continue;
            }

            $docblock = new DocBlox_Reflection_DocBlock($method->getDocComment());

            /** @var DocBlox_Reflection_Tag $event */
            foreach ($docblock->getTagsByName('docblox-event') as $event) {
                $this->event_dispatcher->connect(
                    $event->getDescription(),
                    array($this, $method->getName())
                );
            }
        }
    }
}
