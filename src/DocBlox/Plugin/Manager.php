<?php

class DocBlox_Plugin_Manager
{
    /** @var sfEventDispatcher */
    protected $event_dispatcher = null;

    /** @var DocBlox_Core_Config */
    protected $configuration = null;

    /** @var ZendX_Loader_StandardAutoloader */
    protected $autoloader = null;

    /** @var DocBlox_Plugin */
    protected $plugins = array();

    public function __construct($event_dispatcher, $configuration, $autoloader)
    {
        $this->event_dispatcher = $event_dispatcher;
        $this->configuration    = $configuration;
        $this->autoloader       = $autoloader;
    }

    public function loadFromConfiguration(DocBlox_Core_Config $config)
    {
        $plugins = DocBlox_Core_Abstract::config()->plugins
            ? DocBlox_Core_Abstract::config()->plugins->plugin
            : array();

        // no plugins? then load core
        if (count($plugins) < 1) {
            DocBlox_Core_Abstract::config()->plugins[]
                = new DocBlox_Core_Config('<path>Core</path>');
        }

        // Zend_Config has a quirk; if there is only one entry then it is not
        // wrapped in an array, since we need that we re-wrap it
        if (isset($plugins->path)) {
            $plugins = array($plugins);
        }

        // add new plugins
        foreach ($plugins as $plugin_config) {
            $plugin = new DocBlox_Plugin(
                $this->event_dispatcher, $this->configuration
            );
            $plugin->load($plugin_config->path);

            $plugins[] = $plugin;
        }
    }

}
