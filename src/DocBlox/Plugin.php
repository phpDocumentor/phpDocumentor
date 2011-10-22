<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category  DocBlox
 * @package   Plugin
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://docblox-project.org
 */

/**
 * This class represents a single plugin with all it's options and properties.
 *
 * @category DocBlox
 * @package  Plugin
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://docblox-project.org
 */
class DocBlox_Plugin extends DocBlox_Plugin_Abstract
{
    /** @var string name of the plugin, must be unique in DocBlox */
    protected $name;

    /** @var string version number according to semantic versioning, i.e. 1.0.0 */
    protected $version;

    /** @var string Description for this plugin */
    protected $description;

    /** @var string Author's full name*/
    protected $author;

    /** @var string E-mail address of the author or for support */
    protected $email;

    /** @var string Website where to get more info for this plugin */
    protected $website;

    /** @var string The prefix for the classes in this plugin */
    protected $class_prefix = 'DocBlox_Plugin';

    /** @var string[] a list of listener classes to register */
    protected $listeners = array();

    /** @var string[] list of names of plugins which this depends on */
    protected $dependencies = array();

    /** @var string[] associative array with options */
    protected $options = array();

    /**
     * Loads the plugin's definition from the given XML configuration file.
     *
     * If the autoloader is provided then the class' prefix is added to it.
     *
     * @param string                               $file       Path to the
     *  configuration file.
     * @param ZendX_Loader_StandardAutoloader|null $autoloader Autoloader object
     *  to add the prefix/path combination to.
     *
     * @return void
     */
    public function load($file, $autoloader = null)
    {
        $path = $file;
        if (preg_match('/^[a-zA-Z0-9\_]+$/', $path)) {
            $path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Plugin'
                . DIRECTORY_SEPARATOR . $path;
        }

        $xml = simplexml_load_file(
            rtrim($path, '/\\') . DIRECTORY_SEPARATOR . 'plugin.xml'
        );

        $this->name         = $xml->name;
        $this->version      = $xml->version;
        $this->description  = $xml->description;
        $this->author       = $xml->author;
        $this->email        = $xml->email;
        $this->website      = $xml->website;
        $this->class_prefix = isset($xml->class_prefix)
            ? (string)$xml->class_prefix
            : 'DocBlox_Plugin_'
              . str_replace(' ', '', ucwords((string)$this->name));

        if ($autoloader) {
            $autoloader->registerPrefix($this->class_prefix, $path);
        }

        $listeners = !is_array($xml->listener)
            ? $xml->listener
            : array($xml->listener);

        foreach ($listeners as $listener) {
            $class = $this->class_prefix . '_' . (string)$listener;
            $this->listeners[] = new $class($this);
        }

        $options = !is_array($xml->options)
            ? $xml->options
            : array($xml->options);

        foreach ($options->option as $option) {
            $key = (string)$option['name'];
            $this->options[$key] = $option;
        }
    }

    /**
     * Return the options that have been set
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}