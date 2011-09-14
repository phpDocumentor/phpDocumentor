<?php
class DocBlox_Plugin_Abstract
{
    protected $event_dispatcher = null;
    protected $configuration = null;

    function __construct($event_dispatcher, $configuration)
    {
        $this->event_dispatcher = $event_dispatcher;
        $this->configuration = $configuration;
    }

}
