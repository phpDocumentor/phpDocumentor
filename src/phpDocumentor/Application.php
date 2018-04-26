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
use phpDocumentor\Parser\Event\PreFileEvent;
use phpDocumentor\Translator\Translator;
use Psr\Container\ContainerInterface;
use Psr\Log\LogLevel;
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
use phpDocumentor\Application\Stage\Parser as ParserStage;
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
    public function __construct(LoggerInterface $logger, Translator $translator, ContainerInterface $container)
    {
        parent::__construct($container);

        $this->defineIniSettings();
        $this->register(new Plugin\ServiceProvider());

        Dispatcher::getInstance()->addListener(
            'parser.file.pre',
            function (PreFileEvent $event) use ($logger) {
                $logger->log(LogLevel::INFO, 'Parsing ' . $event->getFile());
            }
        );

        Dispatcher::getInstance()->addListener('system.log', function(LogEvent $e) use ($logger, $translator) {
            $logger->log($e->getPriority(), $translator->translate($e->getMessage()), $e->getContext());
        });

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
}
