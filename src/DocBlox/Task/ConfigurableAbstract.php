<?php
/**
 * DocBlox
 *
 * @category   DocBlox
 * @package    Tasks
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */

/**
 * Abstract base class for working with tasks who rely on the configuration.
 *
 * @category   DocBlox
 * @package    Tasks
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 *
 * @method getConfig
 * @method setConfig
 */
abstract class DocBlox_Task_ConfigurableAbstract extends DocBlox_Task_Abstract
{
  /**
   * Override constructor to temporarily wait with defining rules.
   */
  public function __construct()
  {
    parent::__construct();

    // we always offer a configuration option
    $this->addOption(
      'c|config', '-s',
      'Configuration filename OR "none", when this option is omitted DocBlox tries to load the docblox.xml or '
        . 'docblox.dist.xml from the current working directory'
    );
  }

  /**
   * Additionally checks whether the given filename is readable.
   *
   * @throws InvalidArgumentException
   *
   * @param string $value
   *
   * @return void
   */
  public function setConfig($value)
  {
    if ($value && !is_readable($value) && (strtolower($value) !== 'none'))
    {
      throw new InvalidArgumentException('Config file "' . $value . '" is not readable');
    }

    parent::setConfig($value);
  }


  /**
   * Merge the config files before population.
   *
   * @return void
   */
  protected function prePopulate()
  {
    // prevent the loading of configuration files by specifying 'none'.
    if (strtolower($this->getConfig()) == 'none')
    {
      return;
    }

    if ($this->getConfig())
    {
      // when the configuration parameter is provided; merge that with the basic config
      DocBlox_Core_Abstract::config()->merge(new Zend_Config_Xml($this->getConfig()));
    }
    elseif (is_readable('docblox.xml'))
    {
      // when the configuration is not provided; check for the presence of a configuration file in the current directory
      // and merge that
      DocBlox_Core_Abstract::config()->merge(new Zend_Config_Xml('docblox.xml'));
    }
    elseif (is_readable('docblox.dist.xml'))
    {
      // when no docblox.xml is provided; check for a dist.xml file. Yes, compared to, for example, PHPUnit the xml
      // and dist is reversed; this is done on purpose so IDEs have an easier time on it.
      DocBlox_Core_Abstract::config()->merge(new Zend_Config_Xml('docblox.dist.xml'));
    }
  }
}
