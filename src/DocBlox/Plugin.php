<?php
class DocBlox_Plugin extends DocBlox_Plugin_Abstract
{
    protected $name;

    protected $version;

    protected $description;

    protected $author;

    protected $email;

    protected $website;

    protected $class_prefix = 'DocBlox_Plugin';

    protected $listeners = array();

    protected $dependencies = array();

    protected $options = array();

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
        $this->version      = $xml->name;
        $this->description  = $xml->description;
        $this->author       = $xml->author;
        $this->email        = $xml->email;
        $this->website      = $xml->website;
        $this->class_prefix = isset($xml->class_prefix)
            ? (string)$xml->class_prefix
            : 'DocBlox_Plugin_' . str_replace(' ', '', ucwords((string)$this->name));

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

        foreach ($options as $option) {
            $this->options[$option['name']] = (string)$option;
        }
    }

}