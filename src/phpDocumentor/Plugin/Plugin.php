<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */
namespace phpDocumentor\Plugin;

/**
 * This class represents a single plugin with all it's options and properties.
 *
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://phpdoc.org
 */
class Plugin extends PluginAbstract
{
    /** @var string name of the plugin, must be unique in phpDocumentor */
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
    protected $class_prefix = '\phpDocumentor\Plugin';

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
     * @param string                     $path       Path to the
     *  configuration file.
     * @param \Composer\ClassLoader|null $autoloader Autoloader object
     *  to add the prefix/path combination to.
     *
     * @return void
     */
    public function load($path, $autoloader = null)
    {
        if (preg_match('/^[a-zA-Z0-9\_]+$/', $path)) {
            $path = dirname(__FILE__) . DIRECTORY_SEPARATOR . $path;
        }

        $filename = rtrim($path, '/\\') . DIRECTORY_SEPARATOR . 'plugin.xml';
        if (!file_exists($filename)) {
            throw new \InvalidArgumentException(
                'No plugin configuration could be found at ' . $filename
            );
        }
        $xml = simplexml_load_file($filename);

        $this->name         = $xml->name;
        $this->version      = $xml->version;
        $this->description  = $xml->description;
        $this->author       = $xml->author;
        $this->email        = $xml->email;
        $this->website      = $xml->website;
        $this->class_prefix = isset($xml->class_prefix)
            ? (string)$xml->class_prefix
            : '';

        if ($autoloader && $this->class_prefix) {
            $autoloader->add($this->class_prefix, $path);
        }

        $listeners = !is_array($xml->listener)
            ? $xml->listener
            : array($xml->listener);

        foreach ($listeners as $listener) {
            $prefix = ($this->class_prefix) ? $this->class_prefix . '_' : '';
            $class = $prefix . (string)$listener;
            $this->listeners[] = new $class($this);
        }

        $options = !is_array($xml->options)
            ? $xml->options
            : array($xml->options);

        foreach ($options->option as $option) {
            $key = (string)$option['name'];
            $this->options[$key] = $option;
        }

        $this->translate = new \Zend\Translator\Adapter\ArrayAdapter(array(
            'locale' => 'en',
            'content' => $path . DIRECTORY_SEPARATOR . 'Messages'
                . DIRECTORY_SEPARATOR . 'en.php'
        ));

        /** @var \DirectoryIterator[] $files  */
        $files = new \DirectoryIterator($path . DIRECTORY_SEPARATOR . 'Messages');
        foreach($files as $path) {
            $base_name = $path->getBasename('.php');
            if (!$path->isFile() || ($base_name == 'en')) {
                continue;
            }

            $this->translate->addTranslation(array(
                'locale' => $base_name,
                'content' => $path->getPath()
            ));
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