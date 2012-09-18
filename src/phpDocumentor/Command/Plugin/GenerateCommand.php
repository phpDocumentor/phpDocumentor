<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */
namespace phpDocumentor\Command\Plugin;

use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Output\OutputInterface;

/**
 * Generates a skeleton plugin.
 */
class GenerateCommand extends \Cilex\Command\Command
{
    /**
     * Initializes this command and sets the name, description, options and
     * arguments.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('plugin:generate')
            ->setDescription(
                'Generates a skeleton plugin'
            )
            ->addOption(
                'target', 't',
                InputOption::VALUE_REQUIRED,
                'Target location where to generate the new plugin'
            )
            ->addOption(
                'name', null, InputOption::VALUE_REQUIRED,
                'The name for the new plugin'
            )
            ->addOption(
                'author', 'a', InputOption::VALUE_OPTIONAL,
                'Name of the author'
            )
            ->addOption(
                'given-version', null, InputOption::VALUE_OPTIONAL,
                'Version number of this plugin'
            )
            ->addOption(
                'force', null, InputOption::VALUE_NONE,
                'Forces generation of the new plugin, even if there '
                . 'is an existing plugin at that location'
            );
    }

    /**
     * Executes the business logic involved with this command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $target  = $input->getOption('target');
        $name    = $input->getOption('name');

        $this->validateTargetOption($target);
        $this->validateNameOption($name);

        $path = $this->getDestinationLocation($target, $name);
        $this->prepareLocation($path, $input->getOption('force'));

        $output->writeln('Generating files');

        $this->generateConfigurationFile(
            $path,
            $name,
            $this->getVersion($input),
            $input->getOption('author')
        );
        $this->generateListenerFile($path, $name);
        $this->generateBaseException($path, $name);

        $output->writeln('Finished generating a new plugin at: ' . $path);
        $output->writeln('');

        return 0;
    }

    /**
     * Validates whether the given plugin name is not empty.
     *
     * @param string $name
     *
     * @throws \InvalidArgumentException if no name is provided
     *
     * @return void
     */
    protected function validateNameOption($name)
    {
        if ($name == '') {
            throw new \InvalidArgumentException('No plugin name has been given');
        }
    }

    /**
     * Validates whether the given target location exists and is writable.
     *
     * @param string $target
     *
     * @throws \InvalidArgumentException if the location does not exist
     * @throws \InvalidArgumentException is the location is not writable
     *
     * @return void
     */
    protected function validateTargetOption($target)
    {
        if (!file_exists($target) || !is_dir($target)) {
            throw new \InvalidArgumentException(
                'Target path "' . $target . '" must exist'
            );
        }

        if (!is_writable($target)) {
            throw new \InvalidArgumentException(
                'Target path "'.$target.'" is not writable'
            );
        }
    }

    /**
     * Reads the version number from the input and returns it.
     *
     * @param InputInterface $input
     *
     * @return string
     */
    protected function getVersion(InputInterface $input)
    {
        return $input->getOption('given-version')
            ? $input->getOption('given-version') : '1.0.0';
    }

    /**
     * Retrieves the destination location name.
     *
     * @param string $target The target base location
     * @param string $name   The plugin's name
     *
     * @return string
     */
    protected function getDestinationLocation($target, $name)
    {
        return $target . DIRECTORY_SEPARATOR . $name;
    }

    /**
     * Pre-generates the destination location path.
     *
     * @param string $path             The location for the plugin
     * @param bool   $remove_if_exists Forcibly removes the previous plugin
     *
     * @throws \Exception if the folder already exists and $remove_if_exists is
     *     false.
     *
     * @return void
     */
    protected function prepareLocation($path, $remove_if_exists = false)
    {
        if (file_exists($path)) {
            if (!$remove_if_exists) {
                throw new \Exception(
                    'The folder "' . $path . '" already exists'
                );
            } else {
                echo 'Removing previous plugin' . PHP_EOL;
                `rm -rf $path`;
            }
        }

        echo 'Generating directory structure' . PHP_EOL;
        mkdir($path);
    }

    /**
     * Generate a default configuration file.
     *
     * @param string $path    Where to generate the config file.
     * @param string $name    The name of the plugin.
     * @param string $version The version number for the plugin.
     * @param string $author  The author's name.
     *
     * @return void
     */
    protected function generateConfigurationFile(
        $path, $name, $version, $author
    ) {
        $class_part = ucfirst($name);

        file_put_contents(
            $path . DIRECTORY_SEPARATOR . 'plugin.xml',
<<<XML
<?xml version="1.0" encoding="UTF-8" ?>

<plugin>
    <name>{$name}</name>
    <version>$version</version>
    <author>{$author}</author>
    <email></email>
    <description>Please enter a description here</description>
    <class-prefix>\phpDocumentor\Plugin\{$class_part}</class-prefix>
    <listener>Listener</listener>
    <dependencies>
        <phpdoc><min-version>2.0.0</min-version></phpdoc>
    </dependencies>
    <options>
    </options>
</plugin>
XML
        );
    }

    /**
     * Generates base exception for this plugin.
     *
     * It is recommended for plugins to not rely on phpDocumentor's Exceptions
     * but provide their own. This task will aid by pre-generating own for
     * the author.
     *
     * @param string $path Location for the exception class.
     * @param string $name Plugin name to use in the namespace.
     *
     * @return void
     */
    protected function generateBaseException($path, $name)
    {
        $class_part = ucfirst($name);

        file_put_contents(
            $path . DIRECTORY_SEPARATOR . 'Exception.php',
<<<PHP
namespace phpDocumentor\Plugin_{$class_part};

class Exception extends \Exception
{
}
PHP
        );
    }

    /**
     * Generate a default listener for this plugin.
     *
     * @param string $path The destination location for the listener.
     * @param string $name The name of the plugin to use in the namespace.
     *
     * @return void
     */
    protected function generateListenerFile($path, $name)
    {
        $class_part = ucfirst($name);

        file_put_contents(
            $path . DIRECTORY_SEPARATOR . 'Listener.php',
<<<PHP
namespace phpDocumentor\Plugin\{$class_part};

class Listener extends \phpDocumentor\Plugin\ListenerAbstract
{
}
PHP
        );
    }

}
