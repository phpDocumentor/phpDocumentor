<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor;

use Cilex\Application as Cilex;
use Cilex\Provider\MonologServiceProvider;
use Doctrine\Common\Annotations\AnnotationRegistry;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Shell;
use Zend\Cache\Storage\Adapter\Filesystem;
use Zend\Cache\Storage\Plugin\Serializer as SerializerPlugin;
use Zend\Config\Factory;
use phpDocumentor\Console\Input\ArgvInput;
use phpDocumentor\Descriptor\ProjectAnalyzer;
use phpDocumentor\Descriptor\Validation;
use phpDocumentor\Parser;
use phpDocumentor\Plugin\Core;

/**
 * Finds and activates the autoloader.
 */
require_once findAutoloader();

/**
 * Application class for phpDocumentor.
 *
 * Can be used as bootstrap when the run method is not invoked.
 */
class Application extends Cilex
{
    public static $VERSION;

    /**
     * Initializes all components used by phpDocumentor.
     */
    public function __construct()
    {
        self::$VERSION = file_get_contents(__DIR__ . '/../../VERSION');

        parent::__construct('phpDocumentor', self::$VERSION);

        $this->addAutoloader();
        $this->addLogging();
        $this->setTimezone();
        $this->addConfiguration();
        $this->addEventDispatcher();

        $this['console']->getHelperSet()->set(
            new Console\Helper\ProgressHelper()
        );

        $this['translator.locale'] = 'en';
        $this['translator'] = $this->share(
            function ($app) {
                $translator = new Translator();
                $translator->setLocale($app['translator.locale']);
                return $translator;
            }
        );

        $this->addSerializer();

        $this->addDescriptorServices();

        $this->register(new Parser\ServiceProvider());
        $this->register(new Transformer\ServiceProvider());

        // TODO: make plugin service provider calls registrable from config
        $this->register(new Core\ServiceProvider());

        $this->addCommandsForProjectNamespace();
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
                 'monolog.logfile' => sys_get_temp_dir() . '/phpdoc.log'
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
                $config_files     = array(__DIR__ . '/../../data/phpdoc.tpl.xml');
                if (is_readable($user_config_file)) {
                    $config_files[] = $user_config_file;
                }

                return Factory::fromFiles($config_files, true);
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
        $this['descriptor.builder.serializer'] = 'PhpSerialize';

        $this['descriptor.cache'] = $this->share(
            function () {
                $cache = new Filesystem();
                $cache->setOptions(
                    array(
                        'namespace' => 'phpdoc-cache',
                        'cache_dir' => sys_get_temp_dir(),
                    )
                );
                $cache->addPlugin(new SerializerPlugin());
                return $cache;
            }
        );

        $this['descriptor.builder.validator'] = $this->share(
            function ($container) {
                return new Validation($container['translator']);
            }
        );

        $this['descriptor.builder'] = $this->share(
            function ($container) {
                $builder = new Descriptor\Builder\Reflector();
                $builder->setValidation($container['descriptor.builder.validator']);
                return $builder;
            }
        );

        $this['descriptor.analyzer'] = function () {
            return new ProjectAnalyzer();
        };
    }

    /**
     * Adds the command to phpDocumentor that belong to the Project namespace.
     *
     * @return void
     */
    protected function addCommandsForProjectNamespace()
    {
        $this->command(new Command\Project\RunCommand());
    }

    /**
     * Adds the serializer to the container
     *
     * @return void
     */
    protected function addSerializer()
    {
        $this['serializer'] = $this->share(
            function () {
                $serializerPath = __DIR__ . '/../../vendor/jms/serializer/src';

                if (!file_exists($serializerPath)) {
                    $serializerPath = __DIR__ . '/../../../../jms/serializer/src';
                }

                AnnotationRegistry::registerAutoloadNamespace(
                    'JMS\Serializer\Annotation',
                    $serializerPath
                );

                return SerializerBuilder::create()->build();
            }
        );
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
        /** @var ConsoleApplication $app  */
        $app = $this['console'];

        if ($interactive) {
            $app = new Shell($app);
        }

        $output = new Console\Output\Output();
        $output->setLogger($this['monolog']);

        $app->run(new ArgvInput(), $output);
    }
}

/**
 * Tries to find the autoloader relative to this file and return its path.
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
