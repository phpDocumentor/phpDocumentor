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
 * This class loads the plugins from the configuration and initializes them.
 *
 * @author  Mike van Riel <mike.vanriel@naenius.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    http://phpdoc.org
 */
class Manager
{
    /** @var \phpDocumentor\Event\Dispatcher */
    protected $event_dispatcher = null;

    /** @var \SimpleXMLElement */
    protected $configuration = null;

    /** @var \Composer\Autoload\ClassLoader*/
    protected $autoloader = null;

    /** @var \phpDocumentor\Plugin\Plugin */
    protected $plugins = array();

    /**
     * Registers the Event Dispatcher, Confguration and Autoloader onto the
     * Manager.
     *
     * @param \phpDocumentor\Event\Dispatcher $event_dispatcher Event dispatcher
     *     that plugins can bind to and where events should be dispatched to.
     * @param \Zend\Config\Config             $configuration    Configuration
     *    file which can be used to load parameters into the plugins.
     * @param \Composer\ClassLoader           $autoloader       Plugins can
     *     additionally load classes; with the autoloader they can register
     *     themselves.
     */
    public function __construct($event_dispatcher, $configuration, $autoloader)
    {
        $this->event_dispatcher = $event_dispatcher;
        $this->configuration    = $configuration;
        $this->autoloader       = $autoloader;
    }

    /**
     * Loads the plugins from the configuration.
     *
     * If no plugins are presented in the configuration then only the 'Core'
     * plugin will be loaded.
     *
     * @return void.
     */
    public function loadFromConfiguration()
    {
        $plugins = isset($this->configuration->plugins)
            ? $this->configuration->plugins->plugin
            : array();

        // \Zend\Config\Config has a quirk; if there is only one entry then it
        // is not wrapped in an array, since we need that we re-wrap it
        if (isset($plugins->path)) {
            $plugins = array($plugins);
        }

        if (empty($plugins)) {
            $plugins[] = 'Core';
        }

        // add new plugins
        foreach ($plugins as $plugin_config) {
            $plugin = new \phpDocumentor\Plugin\Plugin(
                $this->event_dispatcher, $this->configuration
            );

            $plugin->load(
                is_string($plugin_config) ? $plugin_config : $plugin_config->path,
                $this->autoloader
            );

            $this->plugins[] = $plugin;
        }
    }

}
