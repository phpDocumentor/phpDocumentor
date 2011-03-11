<?php
/**
 * Provide a short description for this class.
 *
 * @package    DocBlox
 * @subpackage Tasks
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 */
abstract class DocBlox_Task_Abstract extends Zend_Console_Getopt
{
  protected $rules             = array();
  protected $usage_description = null;
  protected $taskname          = '';

  /**
   * Override constructor to temporarily wait with defining rules.
   */
  public function __construct()
  {
    parent::__construct(array());

    // we always offer a help message
    $this->addOption('h|help', '', 'Show this help message');

    $this->configure();
  }

  /**
   * Overridable method to display a header message on the CLI.
   *
   * @return void
   */
  protected function outputHeader()
  {
    echo 'DocBlox version ' . DocBlox_Abstract::VERSION . PHP_EOL . PHP_EOL;
  }

  /**
   * Parses the configuration options and populates the data store.
   *
   * @return void
   */
  public function parse()
  {
    if ($this->_parsed === true)
    {
      return $this;
    }

    parent::parse();

    $this->outputHeader();

    if ($this->getHelp())
    {
      echo $this->getUsageMessage();
      exit(0);
    }

    // the parse method does not have a hook point to invoke the setter methods; thus we iterate through the options and
    // invoke the setters. If no setter exists the __call method will handle this.
    // We have explicitly kept this intact (as the __call's set does nothing special) to enable subclasses to override
    // the __call and receive the benefits.
    foreach ($this->getOptions() as $value)
    {
      // loop through all aliases to check whether a real method was overridden
      foreach ($this->_rules[$value]['alias'] as $alias)
      {
        $method_name = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $alias)));
        if (method_exists($this, $method_name))
        {
          // found one! execute it and continue to the next
          $this->$method_name($this->getOption($value));
          continue 2;
        }
      }

      // no overridden methods found; just invoke the default name to trigger the __call method
      $this->$method_name($this->getOption($value));
    }
  }

  /**
   * Adds an option rule to the application.
   *
   * @param string[] $flags        Set of flags to support for this option.
   * @param string $parameter_type May be nothing, or an string (s), word (w) or integer (i) prefixed with the
   *                               availability specifier (- for optional and = for required).
   * @param string $description    Help text
   *
   * @return void
   */
  public function addOption($flags, $parameter_type, $description)
  {
    if (!is_array($flags))
    {
      $flags = array($flags);
    }

    $this->addRules(array(implode('|', $flags).$parameter_type => $description));
  }

  /**
   * Sets a description message for this task.
   *
   * @param string $description
   *
   * @return void
   */
  public function setUsageDescription($description)
  {
    $this->usage_description = $description;
  }

  /**
   * Returns the usage description or null if none is set.
   *
   * @return string|null
   */
  public function getUsageDescription()
  {
    return $this->usage_description;
  }

  /**
   * Generates the usage / help message.
   *
   * @return string
   */
  public function getUsageMessage()
  {
    $prog_name = basename($this->_progname);
    $usage = "Usage: \n {$prog_name} {$this->taskname} [options]\n\n";
    if ($this->getUsageDescription())
    {
      echo $this->getUsageDescription()."\n\n";
    }
    $maxLen = 20;
    foreach ($this->_rules as $rule)
    {
      $flags = array();
      if (is_array($rule['alias']))
      {
        foreach ($rule['alias'] as $flag)
        {
          $flags[] = (strlen($flag) == 1 ? '-' : '--') . $flag;
        }
      }
      $linepart['name'] = implode(' [', $flags) . (count($flags) > 1 ? ']' : '');
      if (isset($rule['param']) && $rule['param'] != 'none')
      {
        $linepart['name'] .= ' ';
        $rule['paramType'] = strtoupper($rule['paramType']);
        switch ($rule['param'])
        {
          case 'optional':
            $linepart['name'] .= "[{$rule['paramType']}]";
            break;
          case 'required':
            $linepart['name'] .= "{$rule['paramType']}";
            break;
        }
      }
      if (strlen($linepart['name']) > $maxLen)
      {
        $maxLen = strlen($linepart['name']);
      }
      $linepart['help'] = '';
      if (isset($rule['help']))
      {
        $linepart['help'] .= $rule['help'];
      }
      $lines[] = $linepart;
    }
    foreach ($lines as $linepart)
    {
      $usage .= sprintf("%s %s\n", str_pad($linepart['name'], $maxLen), $linepart['help']);
    }
    return $usage.PHP_EOL;
  }

  /**
   * If the method name is prefixed with 'get', it will try to find the parameter in the options array.
   *
   * @param string $name
   * @param string[] $arguments
   *
   * @return
   */
  public function __call($name, $arguments)
  {
    // convert key from a camel-case term to an underscore one.
    $key = substr($name, 3);
    $key = strtolower(preg_replace(
      array('/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/'),
      array('\\1_\\2', '\\1_\\2'),
      $key
    ));

    switch(substr($name, 0, 3))
    {
      case 'get':
        return $this->$key;
      case 'set':
        $this->$key = reset($arguments);
        return;
    }
  }

  /**
   * Prepare the settings and rules for this Task.
   *
   * @abstract
   * @return void
   */
  protected function configure()
  {

  }

  /**
   * Method containing the actual business rules for this Task.
   *
   * @abstract
   * @return void
   */
  abstract public function execute();
}