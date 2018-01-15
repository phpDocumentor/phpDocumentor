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
use phpDocumentor\Application\Console\Command\Project\RunCommand;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Autoload\ClassLoader;
use League\Pipeline\Pipeline;
use Monolog\ErrorHandler;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use phpDocumentor\Application\Stage\Transform;
use phpDocumentor\Event\Dispatcher;
use phpDocumentor\Parser\ServiceProvider;
use Pimple\Container;
use phpDocumentor\Application\Configuration\ConfigurationFactory;
use phpDocumentor\Application\Configuration\Factory\Version2;
use phpDocumentor\Application\Configuration\Factory\Version3;
use phpDocumentor\Application\Console\Command\Helper\ConfigurationHelper;
use phpDocumentor\Application\Console\Command\Helper\LoggerHelper;
use phpDocumentor\Application\Console\Command\Project\ParseCommand;
use phpDocumentor\Application\Console\Command\Project\TransformCommand;
use phpDocumentor\Application\Console\Command\Template\ListCommand;
use phpDocumentor\Application\Stage\Configure;
use phpDocumentor\Application\Stage\Parser;
use phpDocumentor\DomainModel\Uri;
use phpDocumentor\Event\LogEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Application class for phpDocumentor.
 *
 * Can be used as bootstrap when the run method is not invoked.
 */
class Application extends Cilex
{
    public static function VERSION()
    {
        return trim(file_get_contents(__DIR__ . '/../../VERSION'));
    }

    /**
     * Initializes all components used by phpDocumentor.
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->defineIniSettings();

        $this['kernel.timer.start'] = time();
        $this['kernel.stopwatch'] = function () {
            return new Stopwatch();
        };

        $this->register(new Translator\ServiceProvider());
        $this->register(new Descriptor\ServiceProvider());
        $this->register(new Partials\ServiceProvider());
        $this->register(new Parser\ServiceProvider());
        $this->register(new Transformer\ServiceProvider());
        $this->register(new Plugin\ServiceProvider());

        $this['config.user.path'] = new Uri(
            getcwd(). ((file_exists(getcwd() . '/phpdoc.xml')) ? '/phpdoc.xml' : '/phpdoc.dist.xml')
        );

        $this['config.schema.path']  = __DIR__ . '/data/xsd/phpdoc.xsd';
        $this['config.strategies'] = [ new Version3($this['config.schema.path']), new Version2() ];

        /** @var EventDispatcherInterface $evenDispatcher */
        $evenDispatcher = $this['event_dispatcher'];
        $evenDispatcher->addListener('system.log', function(LogEvent $e) {

            /** @var LoggerInterface $logger */
            $logger = $this['monolog'];

            /** @var Translator $translator */
            $translator = $this['translator'];

            $logger->log($e->getPriority(), $translator->translate($e->getMessage()), $e->getContext());
        });

        $this['application.pipeline'] = function($container) {
            return new Configure(
                new ConfigurationFactory(
                    $container['config.strategies'],
                    $container['config.user.path']
                )
            );
        };

        $this['parser.pipeline'] = function($container) {
            return new Pipeline(
                [
                    new Configure\ParserCache($container['parser.middleware.cache']),
                    new Parser(
                        $container['descriptor.builder'],
                        $container['parser'],
                        $container['parser.fileCollector'],
                        $container['descriptor.cache'],
                        $container['parser.example.finder'],
                        $container['partials'],
                        $container['event_dispatcher']
                    )
                ]
            );
        };

        $this['transformer.pipeline'] = function($container) {
            return new Transform(
                $this['descriptor.builder'],
                $this['transformer'],
                $this['compiler'],
                $this['descriptor.cache'],
                $this['event_dispatcher']
            );
        };

        $this->container['command.run.pipeline'] = function ($container) {
            return new Pipeline(
                [
                    $container['application.pipeline'],
                    $container['parser.pipeline'],
                    $container['transformer.pipeline'],
                ]
            );
        };

        $this->addCommandsForProjectNamespace();

        if (\Phar::running()) {
            $this->addCommandsForPharNamespace();
        }
        $this->container = $container;
    }

    /**
     * Adjust php.ini settings.
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

        if (extension_loaded('Zend Optimizer+') && ini_get('zend_optimizerplus.save_comments') === 0) {
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
     */
    protected function setTimezone()
    {
        if (false === ini_get('date.timezone')
            || (version_compare(phpversion(), '5.4.0', '<') && false === getenv('TZ'))
        ) {
            date_default_timezone_set('UTC');
        }
    }

    /**
     * Adds the command to phpDocumentor that belong to the Project namespace.
     */
    protected function addCommandsForProjectNamespace()
    {
        $this->console->add(
            new RunCommand(
                $this->container['descriptor.builder'],
                $this->container['command.run.pipeline']
            )
        );
        $this->console->add(
            new ParseCommand(
                $this['descriptor.builder'],
                $this['parser'],
                $this['parser.fileCollector'],
                $this['translator'],
                $this['descriptor.cache'],
                $this['parser.example.finder'],
                $this['partials'],
                $this['parser.middleware.cache']
        ));

        $this->console->add(
            new TransformCommand(
                $this['descriptor.builder'],
                $this['transformer'],
                $this['compiler'],
                $this['descriptor.cache'],
                $this['event_dispatcher']
            )
        );

        $this->console->add(
            new ListCommand($this['transformer.template.factory'])
        );
    }

    /**
     * Adds the command to phpDocumentor that belong to the Phar namespace.
     */
    protected function addCommandsForPharNamespace()
    {
        $this->command(new Command\Phar\UpdateCommand());
    }
}
