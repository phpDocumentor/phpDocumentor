<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius. (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */
namespace phpDocumentor\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Zend\Config\Config;
use Zend\Config\Factory;

/**
 * Base class for commands that may make use of the configuration.
 *
 * Provides helper methods and a default argument to work with the configuration
 * files.
 */
class ConfigurableCommand extends Command
{
    /**
     * Add an option 'config' for all children.
     *
     * @return void
     */
    protected function configure()
    {
        $this->addOption(
            'config',
            'c',
            InputOption::VALUE_OPTIONAL,
            'Location of a custom configuration file'
        );

        parent::configure();
    }

    /**
     * Overwrite execute to override the default config file.
     *
     * If an alternative configuration file is provided we override the 'config'
     * element in the Dependency Injection Container with a new instance. This
     * new instance uses the phpdoc.tpl.xml as base configuration and applies
     * the given configuration on top of it.
     *
     * Also, upon providing a custom configuration file, is the current working
     * directory set to the directory containing the configuration file so that
     * all relative paths for directory and file selections (and more) is based
     * on that location.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configFile = $input->getOption('config');

        if ($configFile && $configFile !== 'none') {
            $configFile = realpath($configFile);

            // all relative paths mentioned in the configuration file should
            // be relative to the configuration file.
            // This means that if we provide an alternate configuration file
            // that we need to go to that directory first so that paths can be
            // calculated from there.
            chdir(dirname($configFile));
        }

        $container = $this->getContainer();
        if ($configFile) {
            $container['config'] = $container->share(
                function () use ($configFile) {
                    $files = array(__DIR__ . '/../../../data/phpdoc.tpl.xml');
                    if ($configFile !== 'none') {
                        $files[] = $configFile;
                    }

                    return Factory::fromFiles($files, true);
                }
            );
        }

        $this->getHelper('phpdocumentor_logger')->reconfigureLogger($input, $output, $this);
    }

    /**
     * Returns the value of an option from the command-line parameters,
     * configuration or given default.
     *
     * @param InputInterface $input           Input interface to query for
     *     information
     * @param string         $name            Name of the option to retrieve
     *     from argv
     * @param string|null    $config_path     Path to the config element(s)
     *     containing the value to be used when no option is provided.
     * @param mixed|null     $default         Default value used if there is no
     *     configuration option or path set
     * @param bool           $comma_separated Could the value be a comma
     *     separated string requiring splitting
     *
     * @return string
     */
    public function getOption(
        InputInterface $input,
        $name,
        $config_path = null,
        $default = null,
        $comma_separated = false
    ) {
        $value = $input->getOption($name);

        // find value in config
        if (($value === null || is_array($value) && empty($value))
            && $config_path !== null
        ) {
            $value = $this->getConfigValueFromPath($config_path);
            if ($value === null) {
                return $default;
            }
        }

        // use default if value is still null
        if ($value === null || is_array($value) && empty($value)) {
            return (is_array($value) && $default === null)
                ? array()
                : $default;
        }

        // split comma separated values
        if ($comma_separated
            && (!is_array($value)
                || (count($value) == 1) && is_string(current($value))
            )
        ) {
            $value = (array) $value;
            $value = explode(',', $value[0]);
        }

        return $value;
    }

    /**
     * Returns a value by traversing the configuration tree as if it was a file
     * path.
     *
     * @param string $path Path to the config value separated by '/'.
     *
     * @return Config
     */
    protected function getConfigValueFromPath($path)
    {
        /** @var Config $node  */
        $node = $this->getService('config');

        foreach (explode('/', $path) as $node_name) {
            // premature end of the cycle
            if (!is_object($node)) {
                return null;
            }

            $node = $node->get($node_name);
        }

        if ($node === null) {
            return null;
        }

        return $node instanceof Config
            ? $node->toArray()
            : trim((string)$node);
    }
}
