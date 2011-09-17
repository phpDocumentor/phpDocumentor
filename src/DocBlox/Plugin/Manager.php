<?php

class DocBlox_Plugin_Manager
{
    /** @var sfEventDispatcher */
    protected $event_dispatcher = null;

    /** @var DocBlox_Core_Config */
    protected $configuration = null;

    public function __construct($event_dispatcher, $configuration)
    {
        $this->event_dispatcher = $event_dispatcher;
        $this->configuration    = $configuration;
    }

    public function register($file)
    {
        if (!file_exists($file) || !is_readable($file)) {
            throw new Exception(
                'The plugin file "'.$file.'" must exist and be readable'
            );
        }

        $reflection = new DocBlox_Reflection_File($file);
        $reflection->process();
        $classes = $reflection->getClasses();

        if (count($classes) > 1) {
            throw new Exception('Plugin file should only contain one class');
        }

        if (count($classes) < 1) {
            throw new Exception(
                'Plugin file should did not contain a listener class'
            );
        }

        /** @var DocBlox_Reflection_Class $listener_definition  */
        $listener_definition = reset($classes);

        // initialize the plugin / event listener
        include_once($file);
        $listener_name = $listener_definition->getName();
        $listener      = new $listener_name(
            $this->event_dispatcher,
            $this->configuration
        );

        // connect all events of the each method to the event_dispatcher
        foreach($listener_definition->getMethods() as $method) {
            if (!$method->getDocBlock()) {
                continue;
            }

            /** @var DocBlox_Reflection_Tag $event */
            foreach($method->getDocBlock()->getTagsByName('docblox-event') as $event) {
                $this->event_dispatcher->connect(
                    $event->getDescription(),
                    array($listener, $method->getName())
                );
            }
        }
    }
}
