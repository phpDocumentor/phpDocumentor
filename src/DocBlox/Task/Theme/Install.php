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
 * Installs a theme into DocBlox.
 *
 * Installs a theme from the DocBlox theme repository
 * (http://themes.docblox-project.org).
 * It may be necessary for this task to be executed with `sudo` if the themes
 * path is not writable by the current user.
 *
 * @category    DocBlox
 * @package     Tasks
 * @subpackage  Theme
 * @author      Mike van Riel <mike.vanriel@naenius.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 * @link        http://docblox-project.org
 *
 * @method string getName() Name of the template to install
 */
class DocBlox_Task_Theme_List extends DocBlox_Task_Abstract
{
    /** @var string The name of this task including namespace */
    protected $taskname = 'theme:install';

    /**
     * Configures the parameters which this accepts.
     *
     * @return void
     */
    protected function configure()
    {
        $this->addOption(
            'n|name', '=s',
            'The name for the theme that is to be installed'
        );
        $this->addOption(
            'v|version', '-s',
            'The version of the theme that is to be installed; optional if '
            .'project is installed with PEAR'
        );
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
        if ('@pear_installed@' == 'true') {
            passthru(
                'pear install docblox/DocBlox_Theme_' . $this->getName() . ' '
                . $this->getVersion()
            );
            return;
        }

        $phar = new PharData(
            'http://pear.docblox-project.org/get/DocBlox_Theme_'
            .$this->getName().'-'.$this->getVersion().'.tar'
        );
        $phar->extractTo(
          dirname(__FILE__).'/../../data/themes/'.$this->getName(), null, true
        );
    }

}