<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor;

use Cilex\Application as Cilex;
use Cilex\Provider\JmsSerializerServiceProvider;
use Cilex\Provider\MonologServiceProvider;
use Cilex\Provider\ValidatorServiceProvider;
use Composer\Autoload\ClassLoader;
use Monolog\ErrorHandler;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use phpDocumentor\Command\Helper\ConfigurationHelper;
use phpDocumentor\Command\Helper\LoggerHelper;
use phpDocumentor\Console\Input\ArgvInput;
use phpDocumentor\Console\Output\Output;
use Pimple\Container;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Shell;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Application class for phpDocumentor.
 *
 * Can be used as bootstrap when the run method is not invoked.
 */
class Application implements \ArrayAccess
{
    /** @var string $VERSION represents the version of phpDocumentor as stored in /VERSION */
    public static $VERSION;

    private $container;

    private $console;

    /**
     * Initializes all components used by phpDocumentor.
     *
     * @param ClassLoader $autoloader
     * @param array $values
     */
    public function __construct($autoloader = null, array $values = array())
    {
        $this->defineIniSettings();

        $this->container = new Container();
        foreach ($values as $key => $value) {
            $this->container[$key] = $value;
        }

        self::$VERSION = strpos('@package_version@', '@') === 0
            ? trim(file_get_contents(__DIR__ . '/../../VERSION'))
            : '@package_version@';

        $this->console = new \Symfony\Component\Console\Application('phpDocumentor', self::$VERSION);
        $this->console->getHelperSet()->set(new LoggerHelper($this->container));

        $this->container['console'] = function () {
            return $this->console;
        };

        $this['kernel.timer.start'] = time();
        $this['kernel.stopwatch'] = function () {
            return new Stopwatch();
        };

        $this['autoloader'] = $autoloader;

        $this->container->register(new JmsSerializerServiceProvider());
        $this->container->register(new Configuration\ServiceProvider());

        $this->addEventDispatcher();
        $this->addLogging();

        $this->container->register(new Translator\ServiceProvider());
        $this->container->register(new Descriptor\ServiceProvider());
        $this->container->register(new Partials\ServiceProvider());
        $this->container->register(new Parser\ServiceProvider());
        $this->container->register(new Transformer\ServiceProvider());
        $this->container->register(new Plugin\ServiceProvider());

        $this->addCommandsForProjectNamespace();

        if (\Phar::running()) {
            $this->addCommandsForPharNamespace();
        }
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

        switch ($level) {
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

        $this['monolog.level'] = $level;
        if ($logPath) {
            $logPath = str_replace(
                array('{APP_ROOT}', '{DATE}'),
                array(realpath(__DIR__ . '/../..'), $this['kernel.timer.start']),
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

    public function run()
    {
        /** @var ConsoleApplication $app */
        $app = $this['console'];
        $app->setAutoExit(false);

        $output = new Console\Output\Output();
        $output->setLogger($this['monolog']);

        return $app->run(new ArgvInput(), $output);
    }


    /**
     * Adjust php.ini settings.
     *
     * @return void
     */
    protected function defineIniSettings()
    {
        $this->setTimezone();
        ini_set('memory_limit', -1);

        // this code cannot be tested because we cannot control the system settings in unit tests
        // @codeCoverageIgnoreStart
        if (extension_loaded('Zend OPcache') && ini_get('opcache.enable') && ini_get('opcache.enable_cli')) {
            if (ini_get('opcache.save_comments')) {
                ini_set('opcache.load_comments', 1);
            } else {
                ini_set('opcache.enable', 0);
            }
        }

        if (extension_loaded('Zend Optimizer+') && ini_get('zend_optimizerplus.save_comments') == 0) {
            throw new \RuntimeException('Please enable zend_optimizerplus.save_comments in php.ini.');
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * If the timezone is not set anywhere, set it to UTC.
     *
     * This is done to prevent any warnings being outputted in relation to using
     * date/time functions. What is checked is php.ini, and if the PHP version
     * is prior to 5.4, the TZ environment variable.
     *
     * @link http://php.net/manual/en/function.date-default-timezone-get.php for more information how PHP determines the
     *     default timezone.
     *
     * @codeCoverageIgnore this method is very hard, if not impossible, to unit test and not critical.
     *
     * @return void
     */
    protected function setTimezone()
    {
        if (false == ini_get('date.timezone')
            || (version_compare(phpversion(), '5.4.0', '<') && false === getenv('TZ'))
        ) {
            date_default_timezone_set('UTC');
        }
    }

    /**
     * Adds a logging provider to the container of phpDocumentor.
     *
     * @return void
     */
    protected function addLogging()
    {
        $this->container->register(
            new MonologServiceProvider(),
            array(
                'monolog.name'      => 'phpDocumentor',
                'monolog.logfile'   => sys_get_temp_dir() . '/phpdoc.log',
                'monolog.debugfile' => sys_get_temp_dir() . '/phpdoc.debug.log',
                'monolog.level'     => Logger::INFO,
            )
        );

        $app = $this;
        /** @var Configuration $configuration */
        $configuration = $this['config'];
        $this['monolog.configure'] = $this->container->protect(
            function ($log) use ($app, $configuration) {
                $paths    = $configuration->getLogging()->getPaths();
                $logLevel = $configuration->getLogging()->getLevel();

                $app->configureLogger($log, $logLevel, $paths['default']);
            }
        );

        $this->container->extend(
            'console',
            function (ConsoleApplication $console) use ($configuration) {
                $console->getHelperSet()->set(new ConfigurationHelper($configuration));

                return $console;
            }
        );

        ErrorHandler::register($this['monolog']);
    }

    /**
     * Adds the event dispatcher to phpDocumentor's container.
     *
     * @return void
     */
    protected function addEventDispatcher()
    {
        $this['event_dispatcher'] = function () {
            return Event\Dispatcher::getInstance();
        };
    }

    /**
     * Adds the command to phpDocumentor that belong to the Project namespace.
     *
     * @return void
     */
    protected function addCommandsForProjectNamespace()
    {
        $this->console->add(new Command\Project\RunCommand($this->container['descriptor.builder']));
    }

    /**
     * Adds the command to phpDocumentor that belong to the Phar namespace.
     *
     * @return void
     */
    protected function addCommandsForPharNamespace()
    {
        $this->console->add(new Command\Phar\UpdateCommand());
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return $this->container->offsetExists($offset);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->container[$offset];
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->container[$offset] = $value;
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        $this->container->offsetUnset($offset);
    }
}
