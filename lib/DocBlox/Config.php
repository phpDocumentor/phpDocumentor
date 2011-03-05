<?php
/**
 * Configuration class for DocBlox.
 *
 * This class is responsible for registering and remembering the configuration settings.
 * During initialization several configuration parameters are added and the configurations for the templates are merged.
 *
 * @package    DocBlox
 * @subpackage Configuration
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 */
class DocBlox_Config extends Zend_Config_Xml
{
  /**
   * @param string      $xml      File or XML text representing the configuration.
   * @param string|null $section  which section of the configuration to load.
   */
  public function __construct($xml, $section = null)
  {
    parent::__construct($xml, $section, true);

    if (!isset($this->paths))
    {
      $this->paths = new Zend_Config(array(), true);
    }

    $this->paths->application = realpath(dirname(__FILE__) . '/../..');
    $this->paths->data        = realpath($this->paths->application . '/data');
    $this->paths->templates   = realpath($this->paths->data . '/templates');
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
    foreach ($iterator as $path)
    {
      $config_path = $path->getRealPath() . '/template.xml';
      if ($path->isDir() && !$path->isDot() && is_readable($config_path))
      {
        $basename = $path->getBasename();
        $this->templates->$basename = new Zend_Config_Xml($config_path);
      }
    }
  }
}