<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @category  phpDocumentor
 * @package   Core
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

require_once realpath(dirname(__FILE__)) . '/../markdown.php';

/**
 * This class provides a bootstrap for all application who wish to interface
 * with phpDocumentor.
 *
 * The Boostrapper is responsible for setting up the phpDocumentor autoloader, setting
 * up the event dispatcher and initializing the plugins.
 *
 * @category phpDocumentor
 * @package  Core
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://phpdoc.org
 */
class phpDocumentor_Bootstrap
{
    /**
     * Helper static function to get an instance of this class.
     *
     * Usually used to do a one-line initialization, such as:
     *
     *     phpDocumentor_Bootstrap::createInstance()->initialize();
     *
     * @return phpDocumentor_Bootstrap
     */
    public static function createInstance()
    {
        return new self();
    }

    /**
     * Convenience method that does the complete initialization for phpDocumentor.
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
     * Registers and returns the autoloader for phpDocumentor.
     *
     * phpDocumentor uses the ZF2 autoloader to register the common paths and start
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

        include_once $base_include_folder . '/ZendX/Loader/StandardAutoloader.php';
        $autoloader = new ZendX_Loader_StandardAutoloader(
            array(
                 'prefixes' => array(
                     'Zend'    => $base_include_folder . '/Zend',
                     'phpDocumentor' => $base_include_folder . '/phpDocumentor'
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
     * @param ZendX_Loader_StandardAutoloader $autoloader the autoloader upon
     *  which will the plugins will bind their class prefixes and folders.
     *
     * @return void
     */
    public function registerPlugins($autoloader)
    {
        // initialize the event dispatcher; the include is here explicitly
        // should anyone not want this dependency and thus nto invoke this
        // method
        include_once 'symfony/components/event_dispatcher/lib/sfEventDispatcher.php';
        $dispatcher = new sfEventDispatcher();

        // initialize the plugin manager
        $plugin_manager = new phpDocumentor_Plugin_Manager(
            $dispatcher,
            phpDocumentor_Core_Abstract::config(),
            $autoloader
        );

        $plugin_manager->loadFromConfiguration();

        $this->attachDispatcher($dispatcher);
    }

    /**
     * attaches the event dispatcher to all components.
     *
     * @param sfEventDispatcher $event_dispatcher The event dispatcher to attach.
     *
     * @return void
     */
    protected function attachDispatcher($event_dispatcher)
    {
        phpDocumentor_Parser_Abstract::$event_dispatcher      = $event_dispatcher;
        phpDocumentor_Transformer_Abstract::$event_dispatcher = $event_dispatcher;
        phpDocumentor_Reflection_Abstract::$event_dispatcher  = $event_dispatcher;
    }
}