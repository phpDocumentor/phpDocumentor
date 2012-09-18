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

/**
 * Finds and activates the autoloader.
 */
require_once findAutoloader();

use \Symfony\Component\Console\Input\InputInterface;

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
    const VERSION = '2.0.0a10';

    /**
     * Initializes all components used by phpDocumentor.
     */
    function __construct()
    {
        parent::__construct('phpDocumentor', self::VERSION);

        $this->addAutoloader();
        $this->addLogging();
        $this->setTimezone();
        $this->addConfiguration();
        $this->addEventDispatcher();
        $this->loadPlugins();

        $this['console']->getHelperSet()->set(
            new \phpDocumentor\Console\Helper\ProgressHelper()
        );

        $this->addCommandsForProjectNamespace();
        $this->addCommandsForTemplateNamespace();
        $this->addCommandsForTemplateNamespace();
    }

    /**
     * Run the application and if no command is provided, use project:run.
     *
     * @param bool $interactive Whether to run in interactive mode.
     *
     * @return void
     */
    public function run($interactive = false)
    {
        $app = $this['console'];
        if ($interactive) {
            $app = new \Symfony\Component\Console\Shell($app);
        }

        $app->run(new \phpDocumentor\Console\Input\ArgvInput());
    }

    /**
     * Adds the command to phpDocumentor that belong to the Project namespace.
     *
     * @return void
     */
    protected function addCommandsForProjectNamespace()
    {
        $this->command(new \phpDocumentor\Command\Project\ParseCommand());
        $this->command(new \phpDocumentor\Command\Project\RunCommand());
        $this->command(new \phpDocumentor\Command\Project\TransformCommand());
    }

    /**
     * Adds the command to phpDocumentor that belong to the plugin namespace.
     *
     * @return void
     */
    protected function addCommandsForPluginNamespace()
    {
        $this->command(new \phpDocumentor\Command\Plugin\GenerateCommand());
    }

    /**
     * Adds the command to phpDocumentor that belong to the Template namespace.
     *
     * @return void
     */
    protected function addCommandsForTemplateNamespace()
    {
        $this->command(new \phpDocumentor\Command\Template\GenerateCommand());
        $this->command(new \phpDocumentor\Command\Template\ListCommand());
        $this->command(new \phpDocumentor\Command\Template\PackageCommand());
    }

    /**
     * Instantiates the autoloader and adds it to phpDocumentor's container.
     *
     * @return void
     */
    protected function addAutoloader()
    {
        $this['autoloader'] = include findAutoloader();
    }

    /**
     * Adds a logging provider to the container of phpDocumentor.
     *
     * @return void
     */
    protected function addLogging()
    {
        $this->register(
            new \Cilex\Provider\MonologServiceProvider(),
            array(
                'monolog.name'    => 'phpDocumentor',
                'monolog.logfile' => sys_get_temp_dir().'/phpdoc.log'
            )
        );
    }

    /**
     * If the timezone is not set anywhere, set it to UTC.
     *
     * This is done to prevent any warnings being outputted in relation to using
     * date/time functions. What is checked is php.ini, and if the PHP version
     * is prior to 5.4, the TZ environment variable.
     *
     * @return void
     */
    public function setTimezone()
    {
        if (false === ini_get('date.timezone')
            || (version_compare(phpversion(), '5.4.0', '<')
            && false === getenv('TZ'))
        ) {
            date_default_timezone_set('UTC');
        }
    }

    /**
     * Adds the Configuration object to the DIC.
     *
     * phpDocumentor first loads the template config file (/data/phpdoc.tpl.xml)
     * and then the phpdoc.dist.xml, or the phpdoc.xml if it exists but not both,
     * from the current working directory.
     *
     * The user config file (either phpdox.dist.xml or phpdoc.xml) is merged
     * with the template file.
     *
     * @return void
     */
    protected function addConfiguration()
    {
        $this['config'] = $this->share(
            function () {
                $user_config_file = (file_exists('phpdoc.xml'))
                    ? 'phpdoc.xml'
                    : 'phpdoc.dist.xml';
                $config_files = array(__DIR__ . '/../../data/phpdoc.tpl.xml');
                if (is_readable($user_config_file)) {
                    $config_files[] = $user_config_file;
                }

                return \Zend\Config\Factory::fromFiles($config_files, true);
            }
        );
    }

    /**
     * Adds the event dispatcher to phpDocumentor's container.
     *
     * @return void
     */
    protected function addEventDispatcher()
    {
        $this['event_dispatcher'] = $this->share(
            function () {
                return Event\Dispatcher::getInstance();
            }
        );
    }

    /**
     * Load the plugins.
     *
     * phpDocumentor instantiates the plugin manager given the Event Dispatcher,
     * Configuration and autoloader.
     * Using this manager it will read the configuration and load the required
     * plugins.
     *
     * @return void
     */
    protected function loadPlugins()
    {
        $app = $this;
        $this['plugin_manager'] = $this->share(
            function () use ($app) {
                $manager = new \phpDocumentor\Plugin\Manager(
                    $app['event_dispatcher'], $app['config'], $app['autoloader']
                );
                return $manager;
            }
        );
        $this['plugin_manager']->loadFromConfiguration();
    }
}

/**
 * Tries to find the autoloader relative to ththis file and return its path.
 *
 * @throws \RuntimeException if the autoloader could not be found.
 *
 * @return string the path of the autoloader.
 */
function findAutoloader()
{
    $autoloader_base_path = '/../../vendor/autoload.php';

    // if the file does not exist from a base path it is included as vendor
    $autoloader_location = file_exists(__DIR__ . $autoloader_base_path)
        ? __DIR__ . $autoloader_base_path
        : __DIR__ . '/../../..' . $autoloader_base_path;

    if (!file_exists($autoloader_location)) {
        throw new \RuntimeException(
            'Unable to find autoloader at ' . $autoloader_location
        );
    }

    return $autoloader_location;
}
