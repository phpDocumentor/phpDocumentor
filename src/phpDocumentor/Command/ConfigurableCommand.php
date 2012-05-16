<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius. (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */
namespace phpDocumentor\Command;

use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Output\OutputInterface;
use \Symfony\Component\Console\Input\InputOption;

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
            'config', 'c', InputOption::VALUE_OPTIONAL,
            'Location of a custom configuration file'
        );
    }

    /**
     * Overwrite execute to override the default config file.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config_file = $input->getOption('config');
        if ($config_file) {
            $this->container['config'] = $this->container->share(
                function () use ($config_file) {
                    $files = array(__DIR__ . '/../../../data/phpdoc.tpl.xml');
                    if ($config_file !== 'none') {
                        $files[] = $config_file;
                    }

                    return \Zend\Config\Factory::fromFiles($files, true);
                }
            );
        }
    }

    /**
     * Returns the value of an option from the command-line parameters,
     * configuration or given default.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *     Input interface to query for information
     * @param string                                          $name
     *     Name of the option to retrieve from argv
     * @param string|null                                     $config_path
     *     Path to the config element(s) containing the value to be used when
     *     no option is provided.
     * @param mixed|null                                      $default
     *     Default value used if there is no configuration option or path set
     *
     * @return string
     */
    public function getOption(InputInterface $input, $name, $config_path = null,
        $default = null
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

        return $value;
    }

    /**
     * Returns a value by traversing the configuration tree as if it was a file
     * path.
     *
     * @param string $path Path to the config value separated by '/'.
     *
     * @return \Zend\Config\Config
     */
    protected function getConfigValueFromPath($path)
    {
        /** @var \Zend\Config\Config $node  */
        $node = $this->getService('config');
        foreach (explode('/', $path) as $node_name) {
            $node = $node->get($node_name);
        }

        if ($node === null) {
            return null;
        }

        return $node instanceof \Zend\Config\Config
            ? $node->toArray()
            : (string)$node;
    }

}