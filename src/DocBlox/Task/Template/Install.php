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
 * Installs a template into DocBlox.
 *
 * Installs a template from the DocBlox template repository
 * (http://templates.docblox-project.org).
 *
 * The first argument is the name of the template to install.
 *
 * It may be necessary for this task to be executed with `sudo` if the template's
 * path is not writable by the current user. Please note that if DocBlox is not
 * installed using PEAR you need to provide the version number.
 *
 * @category    DocBlox
 * @package     Tasks
 * @subpackage  Template
 * @author      Mike van Riel <mike.vanriel@naenius.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 * @link        http://docblox-project.org
 *
 * @method string getVersion() Version of the template to install
 */
class DocBlox_Task_Template_Install extends DocBlox_Task_Abstract
{
    /** @var string The name of this task including namespace */
    protected $taskname = 'template:install';

    /**
     * Configures the parameters which this accepts.
     *
     * @return void
     */
    protected function configure()
    {
        $this->addOption(
            'v|version', '-s',
            'The version of the template that is to be installed; optional if '
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
        $args = $this->getRemainingArgs();
        if (!isset($args[1])) {
            throw new Exception('Missing template argument');
        }

        $template = $args[1];
        if ('@php_bin@' !== '@'.'php_bin@') {
            passthru(
                'pear install docblox/DocBlox_Template_' . $template . ' '
                . $this->getVersion()
            );
            return;
        }

        if (!$this->getVersion()) {
            throw new Exception(
                'Version number is required if DocBlox is not installed via PEAR'
            );
        }

        $source = 'http://pear.docblox-project.org/get/DocBlox_Template_'
            . $template . '-' . $this->getVersion() . '.tar';

        $tmp = tempnam(sys_get_temp_dir(), 'DBX').'.tar';
        file_put_contents($tmp, file_get_contents($source));
        $folder = realpath(dirname(__FILE__) . '/../../../../data/templates')
            . DIRECTORY_SEPARATOR . $template;

        echo 'Installing to: '.$folder.PHP_EOL;

        $tmp_folder = sys_get_temp_dir() . DIRECTORY_SEPARATOR
            . 'DBX_TEMPLATE_INSTALL' . $template . '-' . $this->getVersion();
        $phar = new PharData($tmp);
        $phar->extractTo($tmp_folder, null,  true);
        unlink($tmp);

        $this->copyRecursive(
            $tmp_folder . DIRECTORY_SEPARATOR . 'DocBlox_Template_' . $template
            . '-' . $this->getVersion(),
            $folder
        );

        echo 'Completed installation'.PHP_EOL;
    }

    /**
     * Copies a file or folder recursively to another location.
     *
     * @param string $src The source location to copy
     * @param string $dst The destination location to copy to
     *
     * @throws Exception if $src does not exist or $dst is not writable
     *
     * @return void
     */
    protected function copyRecursive($src, $dst)
    {
        // if $src is a normal file we can do a regular copy action
        if (is_file($src)) {
            copy($src, $dst);
            return;
        }

        $dir = opendir($src);
        if (!$dir) {
            throw new Exception('Unable to locate path "' . $src . '"');
        }

        // check if the folder exists, otherwise create it
        if ((!file_exists($dst)) && (false === mkdir($dst))) {
            throw new Exception('Unable to create folder "' . $dst . '"');
        }

        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->copyRecursive($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

}