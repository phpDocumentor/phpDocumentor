<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category  DocBlox
 * @package   Tasks
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://docblox-project.org
 */

/**
 * Displays a listing of all available templates in DocBlox
 *
 * This task outputs a list of templates as available in DocBlox.
 * Please mind that custom templates which are situated outside DocBlox are not
 * shown in this listing.
 *
 * @category    DocBlox
 * @package     Tasks
 * @subpackage  Template
 * @author      Mike van Riel <mike.vanriel@naenius.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 * @link        http://docblox-project.org
 */
class DocBlox_Task_Template_List extends DocBlox_Task_Abstract
{
    /** @var string The name of this task including namespace */
    protected $taskname = 'template:list';

    /**
     * Executes the transformation process.
     *
     * @throws Zend_Console_Getopt_Exception
     *
     * @return void
     */
    public function execute()
    {
        if ($this->getQuiet()) {
            return;
        }

        echo 'Available templates:'.PHP_EOL;

        /** @var RecursiveDirectoryIterator $files */
        $files = new DirectoryIterator(dirname(__FILE__).'/../../../../data/templates');
        while ($files->valid()) {
            $name = $files->getBasename();

            // skip abstract files
            if (!$files->isDir() || in_array($name, array('.', '..'))) {
                $files->next();
                continue;
            }

            echo '* '.$name.PHP_EOL;
            $files->next();
        }
        echo PHP_EOL;
    }

}