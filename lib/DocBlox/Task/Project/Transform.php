<?php
/**
 * The transformation task is responsible for transforming the content in the structure file into an output format.
 *
 * @package    DocBlox
 * @subpackage Tasks
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 */
class DocBlox_Task_Project_Transform extends DocBlox_Task_Abstract
{
  protected $taskname = 'project:transform';

  /**
   * Sets the options and description for this task.
   *
   * @return void
   */
  protected function configure()
  {
    $this->setUsageDescription(<<<DESC
DESC
);
    $this->addOption('s|source', '-s',
      'path where the structure.xml is located (optional, defaults to "output/structure.xml")'
    );
    $this->addOption('t|target', '-s',
      'Path where to store the generated output (optional, defaults to "output")'
    );
    $this->addOption('template', '-s',
      'name of the template to use (optional, defaults to "default")'
    );
    $this->addOption('v|verbose', '',
      'Outputs any information collected by this application, may slow down the process slightly'
    );
    $this->addOption('q|quiet', '',
      'Silences the output and logging'
    );
  }

  /**
   * Overwrite header output to not show anything when 'Quiet' mode is on.
   *
   * @return void
   */
  protected function outputHeader()
  {
    if ($this->getQuiet())
    {
      return;
    }

    parent::outputHeader();
  }

  /**
   * Returns the target or the default.
   *
   * @return string
   */
  public function getTarget()
  {
    return parent::getTarget()
      ? parent::getTarget()
      : 'output';
  }

  /**
   * Returns the source structure file location, or the default.
   *
   * @return string
   */
  public function getSource()
  {
    return parent::getSource()
      ? parent::getSource()
      : 'output/structure.xml';
  }

  /**
   * Returns the name of the current template, or the default.
   *
   * @return string
   */
  public function getTemplate()
  {
    return parent::getTemplate()
      ? parent::getTemplate()
      : 'default';
  }

  /**
   * Executes the transformation process.
   *
   * @throws Zend_Console_Getopt_Exception
   *
   * @return void
   */
  public function execute()
  {
    // initialize timer
    $timer = microtime(true);

    // initialize transformer
    $transformer = new DocBlox_Transformer();
    $transformer->setTarget($this->getTarget());
    $transformer->setSource($this->getSource());
    $transformer->setTemplates($this->getTemplate());

    // enable verbose mode if the flag was set
    if ($this->getVerbose())
    {
      $transformer->setLogLevel(DocBlox_Log::DEBUG);
    }
    if ($this->getQuiet())
    {
      $transformer->setLogLevel(DocBlox_Log::QUIET);
    }

    // start the transformation process
    if (!$this->getQuiet())
    {
      echo 'Starting transformation of files (this could take a while depending upon the size of your project)' . PHP_EOL;
    }
    $transformer->execute();
    if (!$this->getQuiet())
    {
      echo 'Finished transformation in ' . round(microtime(true) - $timer, 2) . ' seconds' . PHP_EOL;
    }
  }
}