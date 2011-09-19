<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category  DocBlox
 * @package   Core
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://docblox-project.org
 */

require_once 'markdown.php';

/**
 * This class provides a bootstrap for all application who wish to interface
 * with DocBlox.
 *
 * The Boostrapper is responsible for setting up the DocBlox autoloader, setting
 * up the event dispatcher and initializing the plugins.
 *
 * @category DocBlox
 * @package  Core
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://docblox-project.org
 */
class DocBlox_Bootstrap
{
    /**
     * Helper static function to get an instance of this class.
     *
     * Usually used to do a one-line initialization, such as:
     *
     *     DocBlox_Bootstrap::createInstance()->initialize();
     *
     * @return DocBlox_Bootstrap
     */
    public static function createInstance()
    {
        return new self();
    }

    /**
     * Convenience method that does the complete initialization for DocBlox.
     *
     * This method will register the autoloader, event dispatcher and plugins.
     * The methods called can also be implemented separately, for example when
     * you want to use your own autoloader.
     *
     * @return void
     */
    public function initialize()
    {
        $autoloader = $this->registerAutoloader();
        $this->registerPlugins($autoloader);
    }

    /**
     * Registers and returns the autoloader for DocBlox.
     *
     * DocBlox uses the ZF2 autoloader to register the common paths and start
     * a PSR-0 fallback.
     *
     * The autoloader is also used by the plugin system to make sure that
     * everything in a plugin can be autoloaded.
     *
     * @return ZendX_Loader_StandardAutoloader
     */
    public function registerAutoloader()
    {
        $base_include_folder = dirname(__FILE__) . '/../../src';

        // set path to add lib folder, load the Zend Autoloader
        set_include_path(
            $base_include_folder . PATH_SEPARATOR . get_include_path()
        );

        require_once $base_include_folder . '/ZendX/Loader/StandardAutoloader.php';
        $autoloader = new ZendX_Loader_StandardAutoloader(
            array(
                 'prefixes' => array(
                     'Zend'    => $base_include_folder . '/Zend',
                     'DocBlox' => $base_include_folder . '/DocBlox'
                 ),
                 'fallback_autoloader' => true
            )
        );
        $autoloader->register();

        return $autoloader;
    }

    /**
     * Registers the Event Dispatcher and registers all plugins.
     *
     * @param ZendX_Loader_StandardAutoloader $autoloader
     *
     * @return void
     */
    public function registerPlugins($autoloader)
    {
        // initialize the event dispatcher; the include is here explicitly
        // should anyone not want this dependency and thus nto invoke this
        // method
        require('symfony/components/event_dispatcher/lib/sfEventDispatcher.php');
        $dispatcher = new sfEventDispatcher();

        // initialize the plugin manager
        $plugin_manager = new DocBlox_Plugin_Manager(
            $dispatcher,
            DocBlox_Core_Abstract::config(),
            $autoloader
        );

        $plugins = $this->getPlugins();

        $this->registerPluginObjects($plugin_manager, $plugins);

        $this->attachDispatcher($dispatcher);
    }

    public function getPlugins()
    {
        $plugins = DocBlox_Core_Abstract::config()->plugins
                ? DocBlox_Core_Abstract::config()->plugins->plugin
                : array();

        // no plugins? then load core
        if (count($plugins) < 1) {
            $plugins[] = 'Core';
        }

        // Zend_Config has a quirk; if there is only one entry then it is not
        // wrapped in an array, since we need that we re-wrap it
        if (isset($plugins->path)) {
            $plugins = array($plugins);
        }

        return $plugins;
    }

    protected function registerPluginObjects($manager, $plugins)
    {
        // add new plugins
        foreach ($plugins as $plugin) {
            // if it is not a string we assume it has a 'path' key containing
            // either a path or plugin name
            $plugin = is_string($plugin) ? $plugin : (string)$plugin->path;

            // if it is only a name; surround it with the default path
            if (preg_match('/^[_|A-Z|a-z]+$/', $plugin)) {
                $plugin = dirname(__FILE__) . '/Plugin/' . $plugin . '/Listener.php';
            }

            $manager->register($plugin);
        }
    }

    protected function attachDispatcher($event_dispatcher)
    {
        DocBlox_Parser_Abstract::$event_dispatcher      = $event_dispatcher;
        DocBlox_Transformer_Abstract::$event_dispatcher = $event_dispatcher;
        DocBlox_Reflection_Abstract::$event_dispatcher  = $event_dispatcher;
    }
}