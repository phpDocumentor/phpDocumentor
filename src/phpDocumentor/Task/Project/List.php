<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @category  phpDocumentor
 * @package   Tasks
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

/**
 * Defines all tasks that can be run by phpDocumentor
 *
 * This task outputs a list of tasks grouped by their namespaces.
 *
 * @category   phpDocumentor
 * @package    Tasks
 * @subpackage Project
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */
class phpDocumentor_Task_Project_List extends phpDocumentor_Task_Abstract
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
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(dirname(__FILE__).'/../')
        );
        while ($files->valid()) {
            // skip abstract files
            if (!$files->isFile() || ($files->getBasename() == 'Abstract.php')) {
                $files->next();
                continue;
            }

            // convert the filename to a class
            $class_path = str_replace(
                DIRECTORY_SEPARATOR, '_', $files->getSubPath()
            );
            $class_name = 'phpDocumentor_Task_' . ($class_path ? $class_path. '_' : '')
                . $files->getBasename('.php');

            // check if the class exists, if so: add it to the list
            if (($class_name != 'phpDocumentor_Task_Runner')
                && class_exists($class_name)
            ) {
                $name = $files->getBasename('.php');
                $longest_name = max(strlen($name), $longest_name);
                $results[strtolower($files->getSubPath())][strtolower($name)]
                    = $files->getRealPath();
            }

            $files->next();
        }

        // echo the list of namespaces with their tasks
        ksort($results, SORT_STRING);
        foreach ($results as $namespace => $tasks) {
            echo $namespace.PHP_EOL;

            asort($tasks, SORT_STRING);
            foreach ($tasks as $task => $filename) {
                // get the short description by reflecting the file.
                $refl = new phpDocumentor_Reflection_File($filename, false);
                $refl->dispatch(
                    'system.log.threshold',
                    array(phpDocumentor_Core_Log::QUIET)
                );
                $refl->process();

                /** @var phpDocumentor_Reflection_Class $class */
                $class = current($refl->getClasses());
                echo ' :' . str_pad($task, $longest_name+2)
                     . $class->getDocBlock()->getShortDescription() . PHP_EOL;
            }
        }
        echo PHP_EOL;
    }

}