<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor;
include_once __DIR__ . '/../../vendor/.composer/autoload.php';
/**
 * Application class for phpDocumentor.
 *
 * Can be used as bootstrap when the run method is not invoked.
 *
 * @author  Mike van Riel <mike.vanriel@naenius.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    http://phpdoc.org
 */
class Application extends \Cilex\Application
{
    const VERSION = '2.0.0a3';

    /**
     * Initializes all components used by phpDocumentor.
     *
     * The following is initialized here:
     *
     * * Autoloader (using Composer instead of Cilex' own)
     * * Monolog
     * * Configuration
     * * Event Dispatcher
     * * Plugin Manager
     */
    function __construct()
    {
        parent::__construct('phpDocumentor', self::VERSION);
        $this['autoloader'] = include_once __DIR__
            . '/../../vendor/.composer/autoload.php';

        $this->register(new \Cilex\Provider\MonologServiceProvider(), array(
            'monolog.name'    => 'phpDocumentor',
            'monolog.logfile' => sys_get_temp_dir().'/phpdoc.log'
        ));
        $this->register(new \Cilex\Provider\ConfigServiceProvider(), array(
            'config.path' => 'phpdoc.dist.xml'
        ));

        $app = $this;

        $this['event_dispatcher'] = $this->share(function () {
            new \sfEventDispatcher();
        });

        $this['plugin_manager'] = $this->share(function () use ($app) {
            $manager = new \phpDocumentor_Plugin_Manager(
                $app['event_dispatcher'], $app['config'], $app['autoloader']
            );
            $manager->loadFromConfiguration();
            return $manager;
        });

        $this->command(new \phpDocumentor\Command\Project\ParseCommand());
        $this->command(new \phpDocumentor\Command\Project\RunCommand());
        $this->command(new \phpDocumentor\Command\Project\TransformCommand());
    }
}
