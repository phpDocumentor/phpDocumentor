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
 * Generates a skeleton plugin.
 *
 * @category    DocBlox
 * @package     Tasks
 * @subpackage  Plugin
 * @author      Mike van Riel <mike.vanriel@naenius.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 * @link        http://docblox-project.org
 */
class DocBlox_Task_Plugin_Generate extends DocBlox_Task_Abstract
{
    /** @var string The name of this task including namespace */
    protected $taskname = 'plugin:generate';

    /**
     * Configures the parameters which this accepts
     *
     * @return void
     */
    protected function configure()
    {
        $this->addOption('t|target', '=s',
            'Target location where to generate the new plugin'
        );
        $this->addOption('n|name', '=s',
            'The name for the new plugin'
        );
        $this->addOption('a|author', '-s',
            'Name of the author'
        );
        $this->addOption('v|version', '-s',
            'Version number of this plugin'
        );
        $this->addOption('force', '',
            'Forces generation of the new plugin, even if there is an '
            . 'existing plugin at that location'
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
        // do the sanity checks
        if (!file_exists($this->getTarget()) || !is_dir($this->getTarget())) {
            throw new Exception('Target path "'.$this->getTarget().'" must exist');
        }

        if (!is_writable($this->getTarget())) {
            throw new Exception(
                'Target path "'.$this->getTarget().'" is not writable'
            );
        }

        if ($this->getName() == '') {
            throw new Exception('No plugin name has been given');
        }

        $path = $this->getTarget() . DIRECTORY_SEPARATOR . $this->getName();

        // if the plugin exists, check the force parameter and either throw an
        // exception of remove the existing folder.
        if (file_exists($path)) {
            if (!$this->getForce()) {
                throw new Exception(
                    'The folder "' . $this->getName() . '" already exists at the '
                    . 'target location "' . $this->getTarget() . '"'
                );
            } else {
                echo 'Removing previous plugin'.PHP_EOL;
                `rm -rf $path`;
            }
        }

        $version    = $this->getVersion() ? $this->getVersion() : '1.0.0';
        $class_part = ucfirst($this->getName());

        echo 'Generating directory structure'.PHP_EOL;
        mkdir($path);

        echo 'Generating files' . PHP_EOL;
        file_put_contents(
            $path . DIRECTORY_SEPARATOR . 'plugin.xml',
            <<<XML
<?xml version="1.0" encoding="UTF-8" ?>

<plugin>
    <name>{$this->getName()}</name>
    <version>$version</version>
    <author>{$this->getAuthor()}</author>
    <email></email>
    <description>Please enter a description here</description>
    <class-prefix>DocBlox_Plugin_{$class_part}</class-prefix>
    <listener>Listener</listener>
    <dependencies>
        <docblox><min-version>0.15.0</min-version></docblox>
    </dependencies>
    <options>
    </options>
</plugin>
XML
        );

        file_put_contents(
            $path . DIRECTORY_SEPARATOR . 'Listener.php',
            <<<PHP
class DocBlox_Plugin_{$class_part}_Listener extends DocBlox_Plugin_ListenerAbstract
{
}
PHP
        );

        file_put_contents(
            $path . DIRECTORY_SEPARATOR . 'Exception.php',
            <<<PHP
class DocBlox_Plugin_{$class_part}_Exception extends Exception
{
}
PHP
        );

        echo 'Finished generating a new plugin at: ' . $path . PHP_EOL . PHP_EOL;
    }

}