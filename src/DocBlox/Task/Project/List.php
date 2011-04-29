<?php
/**
 * Defines all tasks that can be run by DocBlox
 *
 * This task outputs a list of tasks grouped by their namespaces.
 *
 * @package    DocBlox
 * @subpackage Tasks
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 */
class DocBlox_Task_Project_List extends DocBlox_Task_Abstract
{
  /** @var string The name of this task including namespace */
  protected $taskname = 'project:list';

  /**
   * Executes the transformation process.
   *
   * @throws Zend_Console_Getopt_Exception
   *
   * @return void
   */
  public function execute()
  {
    $results = array();

    $longest_name = 0;

    /** @var RecursiveDirectoryIterator $files */
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(dirname(__FILE__).'/../'));
    while($files->valid())
    {
      // skip abstract files
      if (!$files->isFile() || ($files->getBasename() == 'Abstract.php'))
      {
        $files->next();
        continue;
      }

      // convert the filename to a class
      $class_name = 'DocBlox_Task_' . str_replace(DIRECTORY_SEPARATOR, '_' , $files->getSubPath())
        . '_' . $files->getBasename('.php');

      // check if the class exists, if so: add it to the list
      if(class_exists($class_name))
      {
        $name = $files->getBasename('.php');
        $longest_name = max(strlen($name), $longest_name);
        $results[strtolower($files->getSubPath())][strtolower($name)] = $files->getRealPath();
      }

      $files->next();
    }

    // echo the list of namespaces with their tasks
    foreach($results as $namespace => $tasks)
    {
      echo $namespace . PHP_EOL;
      foreach ($tasks as $task => $filename)
      {
        // get the short description by reflecting the file.
        $refl = new DocBlox_Reflection_File($filename, false);
        $refl->setLogLevel(DocBlox_Core_Log::QUIET);
        $refl->process();

        /** @var DocBlox_Reflection_Class $class */
        $class = current($refl->getClasses());
        echo ' :' . str_pad($task, $longest_name+2) . $class->getDocBlock()->getShortDescription() . PHP_EOL;
      }
    }
    echo PHP_EOL;
  }

}