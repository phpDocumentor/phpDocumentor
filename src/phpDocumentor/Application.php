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

use Cilex\Application as Cilex;
use Composer\Autoload\ClassLoader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use phpDocumentor\Command\Helper\ConfigurationHelper;
use phpDocumentor\Command\Helper\LoggerHelper;
use phpDocumentor\Command\Phar\UpdateCommand;
use phpDocumentor\Command\Project\RunCommand;
use phpDocumentor\Console\Input\ArgvInput;
use phpDocumentor\Descriptor\Analyzer;
use phpDocumentor\Parser\Command\Project\ParseCommand;
use phpDocumentor\Plugin\Plugin;
use phpDocumentor\Transformer\Command\Project\TransformCommand;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Command\ListCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Shell;
use Symfony\Component\Validator\Validator;

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
     *
     * @param ClassLoader $autoloader
     * @param array       $values
     */
    public function __construct($autoloader = null, array $values = array())
    {
        $builder = new \DI\ContainerBuilder();
        $builder->setDefinitionCache(new \Doctrine\Common\Cache\ArrayCache());
        // propagate the provided options into the container such as ''
        $builder->addDefinitions($values);
        $builder->addDefinitions(__DIR__ . '/ContainerDefinitions.php');
        $builder->useAnnotations(false);
        $phpDiContainer = $builder->build();

        gc_disable();
        $this->defineIniSettings();

        self::$VERSION = strpos('@package_version@', '@') === 0
            ? trim(file_get_contents(__DIR__ . '/../../VERSION'))
            : '@package_version@';

        parent::__construct('phpDocumentor', self::$VERSION, $values);

        $this->extend(
            'console',
            function (ConsoleApplication $console) use ($phpDiContainer) {
                $console->getDefinition()->addOption(
                    new InputOption(
                        'config',
                        'c',
                        InputOption::VALUE_OPTIONAL,
                        'Location of a custom configuration file'
                    )
                );

                $configuration = $phpDiContainer->get('config');
                $console->getHelperSet()->set(new LoggerHelper($phpDiContainer));
                $console->getHelperSet()->set(new ConfigurationHelper($configuration));

                return $console;
            }
        );

        $this->registerPlugins($phpDiContainer);

        $this->command($phpDiContainer->get(ParseCommand::class));
        $this->command($phpDiContainer->get(RunCommand::class));
        $this->command($phpDiContainer->get(TransformCommand::class));
        $this->command($phpDiContainer->get(ListCommand::class));
        if (\Phar::running()) {
            $this->command($phpDiContainer->get(UpdateCommand::class));
        }
    }

    // TODO: Change this; plugins are not read from a config file provided on runtime
    private function registerPlugins($container)
    {
        /** @var Configuration $config */
        $config = $container->get(Configuration::class);

        /** @var Plugin $plugin */
        foreach ($config->getPlugins() as $plugin) {
            $provider = (strpos($plugin->getClassName(), '\\') === false)
                ? sprintf('phpDocumentor\\Plugin\\%s\\ServiceProvider', $plugin->getClassName())
                : $plugin->getClassName();

            try {
                $pluginObject = $container->get($provider);
                call_user_func($pluginObject, $plugin);
            } catch (\InvalidArgumentException $e) {
                throw new \RuntimeException($e->getMessage());
            }
        }
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

        $app->run(new ArgvInput(), new Console\Output\Output());
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
        if (false === ini_get('date.timezone')
            || (version_compare(phpversion(), '5.4.0', '<') && false === getenv('TZ'))
        ) {
            date_default_timezone_set('UTC');
        }
    }
}
