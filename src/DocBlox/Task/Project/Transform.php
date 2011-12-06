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
 * Transforms the structure file into the specified output format
 *
 * This task will execute the transformation rules described in the given template (defaults to 'default') with the
 * given source (defaults to output/structure.xml) and writes these to the target location (defaults to 'output').
 *
 * It is possible for the user to receive additional information using the verbose option or stop additional
 * information using the quiet option. Please take note that the quiet option also disables logging to file.
 *
 * @category    DocBlox
 * @package     Tasks
 * @subpackage  Project
 * @author      Mike van Riel <mike.vanriel@naenius.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 * @link        http://docblox-project.org
 */
class DocBlox_Task_Project_Transform extends DocBlox_Task_Abstract
{
  /** @var string The name of this task including namespace */
  protected $taskname = 'project:transform';

  /**
   * Sets the options and description for this task.
   *
   * @return void
   */
  protected function configure()
  {
    $this->addOption('s|source', '-s',
      'Path where the structure.xml is located (optional, defaults to "output/structure.xml")'
    );
    $this->addOption('t|target', '-s',
      'Path where to store the generated output (optional, defaults to "output")'
    );
    $this->addOption('template', '-s',
      'Name of the template to use (optional, defaults to "default")'
    );
    $this->addOption('v|verbose', '',
      'Outputs any information collected by this application, may slow down the process slightly'
    );
    $this->addOption('parseprivate', '',
      'Whether to parse DocBlocks marked with @internal tag'
    );
    $this->addOption(
        'p|progressbar', '',
        'Whether to show a progress bar; will automatically quiet logging '
        . 'to stdout'
    );
  }

  /**
   * Returns the target or the default.
   *
   * @return string
   */
  public function getTarget()
  {
    $target = parent::getTarget();
    $target = ($target === null)
      ? trim(DocBlox_Core_Abstract::config()->transformer->target)
      : trim($target);

      // if the folder does not exist at all, create it
    if (!file_exists($target)) {
        mkdir($target, 0744, true);
    }

    if (($target == '') || ($target == DIRECTORY_SEPARATOR)) {
      throw new Zend_Console_Getopt_Exception('Either an empty path or root was given: '.$target);
    }

    if (!is_dir($target)) {
      throw new Zend_Console_Getopt_Exception('The given location "' . $target . '" is not a folder.');
    }

    if (!is_writable($target)) {
      throw new Zend_Console_Getopt_Exception(
        'The given path "' . $target . '" either does not exist or is not writable.'
      );
    }

    return realpath($target);
  }

  /**
   * Returns the source structure file location, or the default.
   *
   * Please note that the default is the target location of the parser appended with the structure.xml filename.
   * This is because in a default situation the structure.xml is located in the target folder of the parser.
   *
   * @return string
   */
  public function getSource()
  {
    $source = parent::getSource();
    $source = ($source === null)
      ? trim(DocBlox_Core_Abstract::config()->parser->target).'/structure.xml'
      : trim($source);

    if (($source == '') || ($source == DIRECTORY_SEPARATOR))
    {
      throw new Zend_Console_Getopt_Exception('Either an empty path or root was given: ' . $source);
    }

    if (is_dir($source))
    {
      throw new Zend_Console_Getopt_Exception(
        'The given path "' . $source . '" is a folder; we expect the exact location of the structure file '
          . '(i.e. data/output/structure.xml)'
      );
    }

    if (!is_readable($source))
    {
      throw new Zend_Console_Getopt_Exception('The given path "' . $source . '" either does not exist or is not readable.');
    }

    return realpath($source);
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
      : DocBlox_Core_Abstract::config()->transformations->template->name;
  }

    public function echoProgress(sfEvent $event)
    {
        echo '.';
        if (($event['progress'][0] % 70 == 0)
            || ($event['progress'][0] % $event['progress'][1] == 0)
        ) {
            echo ' ' . $event['progress'][0] . '/' . $event['progress'][1] . PHP_EOL;
        }
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

    if ($this->getProgressbar()) {
        DocBlox_Transformer_Abstract::$event_dispatcher->connect(
            'transformer.writer.xsl.pre', array($this, 'echoProgress')
        );
        $this->setQuiet(true);
    }

      // initialize transformer
    $transformer = new DocBlox_Transformer();
    $transformer->setTemplatesPath(
        DocBlox_Core_Abstract::config()->paths->templates
    );
    $transformer->setTarget($this->getTarget());
    $transformer->setSource($this->getSource());
    $transformer->setTemplates($this->getTemplate());
    $transformer->setParseprivate($this->getParseprivate());

      // add links to external docs
      $external_class_documentation = DocBlox_Core_Abstract::config()
          ->getArrayFromPath('transformer/external-class-documentation');

      $external_class_documentation = (!is_numeric(current(array_keys($external_class_documentation))))
          ? array($external_class_documentation)
          : $external_class_documentation;

      /** @var DocBlox_Core_Config $doc */
      foreach($external_class_documentation as $doc) {
          if (empty($doc)) {
              continue;
          }

          $transformer->setExternalClassDoc(
              (string)$doc['prefix'],
              (string)$doc['uri']
          );
      }

    // start the transformation process
    if (!$this->getQuiet())
    {
      echo 'Starting transformation of files (this could take a while depending upon the size of your project)' . PHP_EOL;
    }
    $transformer->execute();
    if (!$this->getQuiet() || $this->getProgressbar())
    {
      echo 'Finished transformation in ' . round(microtime(true) - $timer, 2) . ' seconds' . PHP_EOL;
    }
  }
}