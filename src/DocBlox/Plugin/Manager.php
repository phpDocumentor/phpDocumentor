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
        $plugins = isset(DocBlox_Core_Abstract::config()->plugins)
            ? DocBlox_Core_Abstract::config()->plugins->plugin
            : array();

        // Zend_Config has a quirk; if there is only one entry then it is not
        // wrapped in an array, since we need that we re-wrap it
        if (isset($plugins->path)) {
            $plugins = array($plugins);
        }

        if (empty($plugins)) {
            $plugins[] = 'Core';
        }

        // add new plugins
        foreach ($plugins as $plugin_config) {
            $plugin = new DocBlox_Plugin(
                $this->event_dispatcher, $this->configuration
            );

            $plugin->load(is_string($plugin_config)
                ? $plugin_config
                : $plugin_config->path);

            $this->plugins[] = $plugin;
        }
    }

}
