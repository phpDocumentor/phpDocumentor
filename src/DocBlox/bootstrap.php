<?php
class DocBlox_Bootstrap
{
    protected $base_include_path = '';

    public static function createInstance()
    {
        return new self();
    }

    public function initialize()
    {
        $this->registerAutoloader();
        $this->registerPlugins();
    }

    public function registerAutoloader()
    {
        // determine base include folder, if @php_bin@ contains @php_bin then we do not install via PEAR
        $base_include_folder = (strpos('@php_dir@', '@php_dir') === 0)
                ? dirname(__FILE__) . '/../../src'
                : '@php_dir@/DocBlox/src';

        // set path to add lib folder, load the Zend Autoloader
        set_include_path($base_include_folder . PATH_SEPARATOR . get_include_path());

        require_once $base_include_folder . '/ZendX/Loader/StandardAutoloader.php';
        $autoloader = new ZendX_Loader_StandardAutoloader(
            array(
                 'prefixes' => array(
                     'Zend' => $base_include_folder . '/Zend',
                     'DocBlox' => $base_include_folder . '/DocBlox'
                 ),
                 'fallback_autoloader' => true
            )
        );
        $autoloader->register();
    }

    public function registerPlugins()
    {
        require('symfony/components/event_dispatcher/lib/sfEventDispatcher.php');

        $dispatcher = new sfEventDispatcher();

        $plugin_manager = new DocBlox_Plugin_Manager(
            $dispatcher,
            DocBlox_Core_Abstract::config()
        );

        $plugin_manager->register(
            dirname(__FILE__) . '/Plugin/Core/Listener.php'
        );

        DocBlox_Parser_Abstract::$event_dispatcher      = $dispatcher;
        DocBlox_Transformer_Abstract::$event_dispatcher = $dispatcher;
        DocBlox_Reflection_Abstract::$event_dispatcher  = $dispatcher;
    }
}
