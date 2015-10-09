<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.4
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor;

use Composer\Autoload\ClassLoader;
use phpDocumentor\Application\Cli\Input\ArgvInput;
use Symfony\Component\Console\Application as ConsoleApplication;
use phpDocumentor\Plugin\Plugin;

/**
 * Application class for phpDocumentor.
 *
 * Can be used as bootstrap when the run method is not invoked.
 */
final class Application
{
    /** @var string $VERSION represents the version of phpDocumentor as stored in /VERSION */
    public static $VERSION;

    /**
     * Initializes all components used by phpDocumentor.
     *
     * @param ClassLoader $autoloader
     * @param array       $values
     */
    public function __construct($autoloader = null, array $values = array())
    {
        $container = $this->createContainer($values);

        gc_disable();
        $this->setTimezone();
        $this->removePhpMemoryLimit();
        $this->ensureAnnotationsAreCached();

        self::$VERSION = $container->get('application.version');
        $this->console = $container->get(ConsoleApplication::class);

        $this->registerPlugins($container);
    }

    /**
     * Run the application and if no command is provided, use project:run.
     *
     * @return integer The exit code for this application
     */
    public function run()
    {
        $this->console->setAutoExit(false);

        return $this->console->run(new ArgvInput());
    }

    // TODO: Change this; plugins are not read from a config file provided on runtime
    private function registerPlugins($container)
    {
        //TODO: refactor this method. Previously config was used here.
        /** @var Plugin $plugin */
        foreach (array() as $plugin) {
            // TODO: Retrieving the Plugin should be in a Repository class
            $provider = (strpos($plugin->getClassName(), '\\') === false)
                ? sprintf('phpDocumentor\\Plugin\\%s\\ServiceProvider', $plugin->getClassName())
                : $plugin->getClassName();

            try {
                $pluginObject = $container->get($provider);
                call_user_func($pluginObject, $plugin->getParameters());
            } catch (\InvalidArgumentException $e) {
                throw new \RuntimeException($e->getMessage());
            }
        }
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
    private function setTimezone()
    {
        if (false === ini_get('date.timezone')
            || (version_compare(phpversion(), '5.4.0', '<') && false === getenv('TZ'))
        ) {
            date_default_timezone_set('UTC');
        }
    }

    private function ensureAnnotationsAreCached()
    {
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

    private function removePhpMemoryLimit()
    {
        ini_set('memory_limit', -1);
    }

    /**
     * @param array $values
     * @return \DI\Container
     */
    private function createContainer(array $values)
    {
        $builder = new \DI\ContainerBuilder();
        $builder->addDefinitions($values);
        $builder->addDefinitions(__DIR__ . '/ContainerDefinitions.php');
        $builder->useAnnotations(false);
        $phpDiContainer = $builder->build();
        return $phpDiContainer;
    }
}
