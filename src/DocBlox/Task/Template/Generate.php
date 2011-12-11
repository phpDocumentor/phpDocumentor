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
 * Generates a skeleton template.
 *
 * @category    DocBlox
 * @package     Tasks
 * @subpackage  Template
 * @author      Mike van Riel <mike.vanriel@naenius.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 * @link        http://docblox-project.org
 */
class DocBlox_Task_Template_Generate extends DocBlox_Task_Abstract
{
    /** @var string The name of this task including namespace */
    protected $taskname = 'template:generate';

    /**
     * Configures the parameters which this accepts
     *
     * @return void
     */
    protected function configure()
    {
        $this->addOption('t|target', '=s',
            'Target location where to generate the new template'
        );
        $this->addOption('n|name', '=s',
            'The name for the new template'
        );
        $this->addOption('a|author', '-s',
            'Name of the author'
        );
        $this->addOption('v|version', '-s',
            'Version number of this template'
        );
        $this->addOption('force', '',
            'Forces generation of the new template, even if there is an '
            . 'existing template at that location'
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
            throw new Exception('No template name has been given');
        }

        $path = $this->getTarget() . DIRECTORY_SEPARATOR . $this->getName();

        // if the template exists, checkt he force parameter and either throw an
        // exception of remove the existing folder.
        if (file_exists($path)) {
            if (!$this->getForce()) {
                throw new Exception(
                    'The folder "' . $this->getName() . '" already exists at the '
                    . 'target location "' . $this->getTarget() . '"'
                );
            } else {
                echo 'Removing previous template'.PHP_EOL;
                `rm -rf $path`;
            }
        }

        $css_path = $path . DIRECTORY_SEPARATOR . 'css';


        echo 'Generating directory structure'.PHP_EOL;
        mkdir($path);
        mkdir($css_path);

        echo 'Generating files' . PHP_EOL;
        copy(
            dirname(__FILE__).'/../../../../data/base_template/css/template.css',
            $css_path . DIRECTORY_SEPARATOR . 'template.css'
        );
        copy(
            dirname(__FILE__).'/../../../../data/base_template/index.xsl',
            $path . DIRECTORY_SEPARATOR . 'index.xsl'
        );

        $name    = $this->getName();
        $author  = $this->getAuthor();
        $version = $this->getVersion();

        $template = preg_replace(
            array('/\{\{\s*name\s*\}\}/', '/\{\{\s*version\s*\}\}/', '/\{\{\s*author\s*\}\}/'),
            array($name, $version, $author),
            file_get_contents(
                dirname(__FILE__) . '/../../../../data/base_template/template.xml'
            )
        );

        file_put_contents(
            $path . DIRECTORY_SEPARATOR . 'template.xml', $template
        );

        echo 'Finished generating a new template at: ' . $path . PHP_EOL . PHP_EOL;
    }

}