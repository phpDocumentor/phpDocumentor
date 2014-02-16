<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor;

use Cilex\Application as Cilex;
use Cilex\Provider\MonologServiceProvider;
use Cilex\Provider\ValidatorServiceProvider;
use Doctrine\Common\Annotations\AnnotationRegistry;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Monolog\ErrorHandler;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use phpDocumentor\Command\Helper\LoggerHelper;
use phpDocumentor\Console\Input\ArgvInput;
use phpDocumentor\Transformer\Writer\Exception\RequirementMissing;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Shell;
use Symfony\Component\Stopwatch\Stopwatch;
use Zend\Config\Factory;

/**
 * Finds and activates the autoloader.
 */
require_once findAutoloader();
if (!\Phar::running()) {
    define('DOMPDF_ENABLE_AUTOLOAD', false);
    if (file_exists(__DIR__ . '/../../vendor/dompdf/dompdf/dompdf_config.inc.php')) {
        // when normally installed, get it from the vendor folder
        require_once(__DIR__ . '/../../vendor/dompdf/dompdf/dompdf_config.inc.php');
    } else {
        // when installed using composer, include it from that location
        require_once(__DIR__ . '/../../../../dompdf/dompdf/dompdf_config.inc.php');
    }
}

/**
 * Application class for phpDocumentor.
 *
 * Can be used as bootstrap when the run method is not invoked.
 */
class Application extends Cilex
{
    /** @var string $VERSION represents the version of phpDocumentor as stored in /VERSION */
    public static $VERSION;

    /**
     * Initializes all components used by phpDocumentor.
     */
    public function __construct()
    {
        $this->defineIniSettings();
        
        self::$VERSION = file_get_contents(__DIR__ . '/../../VERSION');

        parent::__construct('phpDocumentor', self::$VERSION);

        $this['kernel.timer.start'] = time();
        $this['kernel.stopwatch'] = function () {
            return new Stopwatch();
        };

        $this->setTimezone();
        $this->addAutoloader();
        $this->addSerializer();
        $this->addConfiguration();
        $this->addLogging();
        $this->addEventDispatcher();
        $this->addTranslator();

        /** @var ConsoleApplication $console */
        $console = $this['console'];
        $console->getHelperSet()->set(new LoggerHelper());

        $this->register(new ValidatorServiceProvider());
        $this->register(new Descriptor\ServiceProvider());
        $this->register(new Parser\ServiceProvider());
        $this->register(new Transformer\ServiceProvider());

        $this->addPlugins();

        $this->verifyWriterRequirementsAndExitIfBroken();
        $this->addCommandsForProjectNamespace();
    }

    /**
     * Adjust php.ini settings.
     * 
     * @return void
     */
    protected function defineIniSettings()
    {
        ini_set('memory_limit', -1);

        if (extension_loaded('opcache')) {
            ini_set('opcache.save_comments', 1);
            ini_set('opcache.load_comments', 1);
        }
    }

    /**
     * Instantiates plugin service providers and adds them to phpDocumentor's container.
     *
     * @return void
     */
    protected function addPlugins()
    {
        $config = $this['config']->toArray();

        if (!isset($config['plugins']['plugin'][0]['path'])) {
            $this->register(new Plugin\Core\ServiceProvider());
            $this->register(new Plugin\Scrybe\ServiceProvider());
            return;
        }

        $app = $this;

        array_walk(
            $config['plugins']['plugin'],
            function ($plugin) use ($app) {
                $provider = (strpos($plugin['path'], '\\') === false)
                    ? sprintf('phpDocumentor\\Plugin\\%s\\ServiceProvider', $plugin['path'])
                    : $plugin['path'];
                if (!class_exists($provider)) {
                    throw new \RuntimeException('Loading Service Provider for ' . $provider . ' failed.');
                }

                try {
                    $app->register(new $provider);
                } catch (\InvalidArgumentException $e) {
                    throw new \RuntimeException($e->getMessage());
                }
            }
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
                 'monolog.name'      => 'phpDocumentor',
                 'monolog.logfile'   => sys_get_temp_dir() . '/phpdoc.log',
                 'monolog.debugfile' => sys_get_temp_dir() . '/phpdoc.debug.log',
                 'monolog.level'     => Logger::INFO,
            )
        );

        $app = $this;
        $this['monolog.configure'] = $this->protect(
            function ($log) use ($app) {
                $level = (string)$app['config']->logging->level;

                // null means the default is used
                $logPath = isset($app['config']->logging->paths->default)
                    ? (string) $app['config']->logging->paths->default
                    : null;

                // null means the default is used
                $debugPath = isset($app['config']->logging->paths->errors)
                    ? (string) $app['config']->logging->paths->errors
                    : null;

                $app->configureLogger($log, $level, $logPath, $debugPath);
            }
        );
        ErrorHandler::register($this['monolog']);
    }

    /**
     * Removes all logging handlers and replaces them with handlers that can write to the given logPath and level.
     *
     * @param Logger  $logger       The logger instance that needs to be configured.
     * @param integer $level        The minimum level that will be written to the normal logfile; matches one of the
     *                              constants in {@see \Monolog\Logger}.
     * @param string  $logPath      The full path where the normal log file needs to be written.
     *
     * @return void
     */
    public function configureLogger($logger, $level, $logPath = null)
    {
        /** @var Logger $monolog */
        $monolog = $logger;

        switch($level) {
            case 'emergency':
            case 'emerg':
                $level = Logger::EMERGENCY;
                break;
            case 'alert':
                $level = Logger::ALERT;
                break;
            case 'critical':
            case 'crit':
                $level = Logger::CRITICAL;
                break;
            case 'error':
            case 'err':
                $level = Logger::ERROR;
                break;
            case 'warning':
            case 'warn':
                $level = Logger::WARNING;
                break;
            case 'notice':
                $level = Logger::NOTICE;
                break;
            case 'info':
                $level = Logger::INFO;
                break;
            case 'debug':
                $level = Logger::DEBUG;
                break;
        }

        $this['monolog.level']   = $level;
        if ($logPath) {
            $logPath = str_replace(
                array('{APP_ROOT}', '{DATE}'),
                array(realpath(__DIR__.'/../..'), $this['kernel.timer.start']),
                $logPath
            );
            $this['monolog.logfile'] = $logPath;
        }

        // remove all handlers from the stack
        try {
            while ($monolog->popHandler()) {
            }
        } catch (\LogicException $e) {
            // popHandler throws an exception when you try to pop the empty stack; to us this is not an
            // error but an indication that the handler stack is empty.
        }

        if ($level === 'quiet') {
            $monolog->pushHandler(new NullHandler());
            return;
        }

        // set our new handlers
        if ($logPath) {
            $monolog->pushHandler(new StreamHandler($logPath, $level));
        } else {
            $monolog->pushHandler(new StreamHandler('php://stdout', $level));
        }
    }

    /**
     * Adds the Configuration object to the DIC.
     *
     * phpDocumentor first loads the template config file (/data/phpdoc.tpl.xml)
     * and then the phpdoc.dist.xml, or the phpdoc.xml if it exists but not both,
     * from the current working directory.
     *
     * The user config file (either phpdoc.dist.xml or phpdoc.xml) is merged
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
     * Adds the message translator to phpDocumentor's container.
     *
     * @return void
     */
    protected function addTranslator()
    {
        $config = $this['config']->toArray();

        $this['translator.locale'] = isset($config['translator']['locale']) ? $config['translator']['locale'] : 'en';

        $this['translator'] = $this->share(
            function ($app) {
                $translator = new Translator();
                $translator->setLocale($app['translator.locale']);

                return $translator;
            }
        );
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
        $app->setAutoExit(false);

        if ($interactive) {
            $app = new Shell($app);
        }

        $output = new Console\Output\Output();
        $output->setLogger($this['monolog']);

        $app->run(new ArgvInput(), $output);
    }

    protected function verifyWriterRequirementsAndExitIfBroken()
    {
        try {
            $this['transformer.writer.collection']->checkRequirements();
        } catch (RequirementMissing $e) {
            $this['monolog']->emerg(
                'phpDocumentor detected that a requirement is missing in your system setup: ' . $e->getMessage()
            );
            exit(1);
        }
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
