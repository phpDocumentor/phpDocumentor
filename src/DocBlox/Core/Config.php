<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category  DocBlox
 * @package   Core
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://docblox-project.org
 */

/**
 * Configuration class for DocBlox.
 *
 * This class is responsible for registering and remembering the configuration
 * settings.
 * During initialization several configuration parameters are added and the
 * configurations for the templates are merged.
 *
 * @category DocBlox
 * @package  Core
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://docblox-project.org
 */
class DocBlox_Core_Config extends Zend_Config_Xml
{
    /**
     * Initializes the configuration.
     *
     * @param string      $xml     File or XML text representing the configuration.
     * @param string|null $section Which section of the configuration to load.
     */
    public function __construct($xml, $section = null)
    {
        parent::__construct($xml, $section, true);

        if (!isset($this->paths)) {
            $this->paths = new Zend_Config(array(), true);
        }

        $this->paths->application = realpath(dirname(__FILE__) . '/../../..');
        $this->paths->data = realpath($this->paths->application . '/data');
        $this->paths->templates = realpath($this->paths->data . '/templates');
        if (!$this->paths->templates) {
            throw new Exception(
                'The templates folder was not found; have you installed a template?'
            );
        }
        $this->mergeTemplateConfigurations();
    }

    /**
     * Merges the configurations of the templates into this configuration.
     *
     * @return void
     */
    protected function mergeTemplateConfigurations()
    {
        $this->templates = array();
        $iterator = new DirectoryIterator($this->paths->templates);

        /** @var DirectoryIterator $path */
        foreach ($iterator as $path) {
            $config_path = $path->getRealPath() . '/template.xml';
            if ($path->isDir() && !$path->isDot() && is_readable($config_path)) {
                $basename = $path->getBasename();
                $this->templates->$basename = new Zend_Config_Xml($config_path);
            }
        }
    }

    /**
     * Returns the items of the given path as an array.
     *
     * A path may be shown as item/item and will always be taken from this
     * object and thus be relative to this object.
     *
     * @param string $path the path from which to retrieve the settings.
     *
     * @return string[]
     */
    public function getArrayFromPath($path)
    {
        $path = explode('/', $path);

        // walk through the path; if any segment was not found; return an
        // empty array
        $config = $this;
        foreach ($path as $part) {
            if (!isset($config->$part)) {
                return array();
            }

            $config = $config->$part;
        }

        if ($config instanceof Zend_Config) {
            $config = $config->toArray();
        }

        if (is_string($config)) {
            $config = array($config);
        }

        return $config;
    }
}