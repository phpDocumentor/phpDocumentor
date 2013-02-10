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

use Symfony\Component\Console\Input\InputInterface;
use Cilex\Application as Cilex;
use Cilex\Provider\MonologServiceProvider;

/**
 * Application class for phpDocumentor.
 *
 * Can be used as bootstrap when the run method is not invoked.
 *
 * @author  Mike van Riel <mike.vanriel@naenius.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    http://phpdoc.org
 */
class Application extends Cilex
{
    const VERSION = '2.0.0a13';

    /**
     * Initializes all components used by phpDocumentor.
     */
    public function __construct()
    {
        parent::__construct('phpDocumentor', self::VERSION);

        $this->addAutoloader();
        $this->addLogging();
        $this->setTimezone();
        $this->addConfiguration();
        $this->addEventDispatcher();
        $this->loadPlugins();

        $this['console']->getHelperSet()->set(
            new Console\Helper\ProgressHelper()
        );

        $this->addDescriptorServices();
        $this->addParserServices();
        $this->addTransformerServices();

        $this->addCommandsForProjectNamespace();
        $this->addCommandsForTemplateNamespace();
        $this->addCommandsForPluginNamespace();
    }

    /**
     * Adds the services to build the descriptors.
     *
     * This method injects the following services into the Dependency Injection Container:
     *
     * * descriptor.serializer, the serializer used to generate the cache
     * * descriptor.builder, the builder used to transform the Reflected information into a series of Descriptors.
     *
     * It is possible to override which serializer is used by overriding the parameter `descriptor.serializer.class`.
     *
     * @return void
     */
    protected function addDescriptorServices()
    {
        $this['descriptor.serializer.class'] = 'phpDocumentor\Descriptor\Serializer\Serialize';

        $this['descriptor.serializer'] = function ($container) {
            return new $container['descriptor.serializer.class']();
        };

        $this['descriptor.builder'] = $this->share(function ($container) {
            $builder = new Descriptor\Builder\Reflector();
            $builder->setSerializer($container['descriptor.serializer']);
            return $builder;
        });
    }

    /**
     * Adds the services to parse a project and generate a statically reflected representation.
     *
     * This method injects the following services into the Dependency Injection Container:
     *
     * * parser, the component responsible for interacting with the Reflection library and the Project Descriptor.
     *
     * @return void
     */
    protected function addParserServices()
    {
        $this['parser'] = $this->share(function () {
            return new Parser\Parser();
        });
    }

    /**
     * Adds the services to transform a Project Descriptor into a series of artifacts based on a given (series of)
     * template(s).
     *
     * This method injects the following services into the Dependency Injection Container,
     *
     * * transformer.behaviour.collection, the series of behaviours that need to be applied before the transformation
     *    process, may be augmented by plugins.
     * * transformer.writer.collection, a pool of writers that the transformer may utilize, may be augmented by plugins.
     * * transformer, the component responsible for transforming the Project Descriptor into a series of artifacts.
     *
     * @return void
     */
    protected function addTransformerServices()
    {
        $this['transformer.behaviour.collection'] = $this->share(function () {
            return new Transformer\Behaviour\Collection();
        });

        $this['transformer.writer.collection'] = $this->share(function () {
            return new Transformer\Writer\Collection();
        });

        $this['transformer'] = $this->share(function ($container) {
            $transformer = new Transformer\Transformer($container['transformer.writer.collection']);
            $transformer->setBehaviours($container['transformer.behaviour.collection']);
            return $transformer;
        });
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
        $this->command(new Command\Project\RunCommand());
        $this->command(new Command\Project\ParseCommand($this['descriptor.builder'], $this['parser']));
        $this->command(new Command\Project\TransformCommand($this['descriptor.builder'], $this['transformer']));
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
            new MonologServiceProvider(),
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
        if (false === ini_get('date.timezone') || (version_compare(phpversion(), '5.4.0', '<')
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
                $user_config_file = (file_exists(getcwd() . DIRECTORY_SEPARATOR . 'phpdoc.xml'))
                    ? getcwd() . DIRECTORY_SEPARATOR . 'phpdoc.xml'
                    : getcwd() . DIRECTORY_SEPARATOR . 'phpdoc.dist.xml';
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
                    $app['event_dispatcher'],
                    $app['config'],
                    $app['autoloader']
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
