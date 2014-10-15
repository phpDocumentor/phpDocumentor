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

namespace phpDocumentor\Configuration;

use JMS\Serializer\Serializer;
use phpDocumentor\Console\Input\ArgvInput;

/**
 * Loads the template and user-defined configuration file from disk and creates a Configuration object from it.
 *
 * This class will merge the template file and the user-defined configuration file together and serialize it into a
 * configuration object (defaults to `phpDocumentor\Configuration`).
 */
class Loader
{
    /** @var Serializer Object used to serialize configuration files to objects. */
    private $serializer;

    /** @var Merger Object that merges variables, including objects. */
    private $merger;

    /**
     * Registers the dependencies with the loader.
     *
     * @param Serializer  $serializer Object used to serialize configuration files to objects.
     * @param Merger      $merger     Object that merges variables, including objects.
     */
    public function __construct(Serializer $serializer, Merger $merger)
    {
        $this->serializer = $serializer;
        $this->merger     = $merger;
    }

    /**
     * Loads the configuration from the provided paths and returns a populated configuration object.
     *
     * @param string $templatePath          Path to configuration file containing default settings.
     * @param string $userConfigurationPath Path to a file containing user overrides.
     * @param string $class                 The class to instantiate and populate with these options.
     *
     * @return object
     */
    public function load($templatePath, $userConfigurationPath, $class = 'phpDocumentor\Configuration')
    {
        $userConfigFilePath = $this->fetchUserConfigFileFromCommandLineOptions();

        if ($this->isValidFile($userConfigFilePath)) {
            chdir(dirname($userConfigFilePath));
        } else {
            $userConfigFilePath = null;
        }

        return $this->createConfigurationObject($templatePath, $userConfigurationPath, $userConfigFilePath, $class);
    }

    /**
     * Reads the `--config`, or `-c`, command line option and returns a path to the configuration file from those
     * options or false if no existing path was given.
     *
     * @return bool|string
     */
    private function fetchUserConfigFileFromCommandLineOptions()
    {
        $input = new ArgvInput();
        $userConfigFilePath = $input->getParameterOption('--config');

        if (!$userConfigFilePath) {
            $userConfigFilePath = $input->getParameterOption('-c');
        }

        if ($userConfigFilePath !== false) {
            $userConfigFilePath = realpath($userConfigFilePath);
        }

        return $userConfigFilePath;
    }

    /**
     * Verifies if the given path is valid and readable.
     *
     * @param bool|string $path
     *
     * @return bool
     */
    private function isValidFile($path)
    {
        return $path && $path != 'none' && is_readable($path);
    }

    /**
     * Combines the given configuration files and serializes a new Configuration object from them.
     *
     * @param string           $templatePath          Path to the template configuration file.
     * @param string           $defaultUserConfigPath Path to the phpdoc.xml or phpdoc,dist.xml in the current working
     *     directory.
     * @param null|bool|string $customUserConfigPath  Path to the user-defined config file given using the command-line.
     * @param string           $class                 Base Configuration class name to construct and populate.
     *
     * @return null|object
     */
    private function createConfigurationObject($templatePath, $defaultUserConfigPath, $customUserConfigPath, $class)
    {
        $config = $this->serializer->deserialize(file_get_contents($templatePath), $class, 'xml');
        $customUserConfigPath = $customUserConfigPath ? : $defaultUserConfigPath;

        if ($customUserConfigPath !== null && is_readable($customUserConfigPath)) {
            $userConfigFile = $this->serializer->deserialize(file_get_contents($customUserConfigPath), $class, 'xml');

            $config = $this->merger->run($config, $userConfigFile);
        }

        return $config;
    }
}
