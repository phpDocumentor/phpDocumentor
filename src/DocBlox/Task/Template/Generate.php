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
        file_put_contents(
            $css_path . DIRECTORY_SEPARATOR . 'template.css',
            <<<CSS
@import url('navigation.css');
@import url('api-content.css');
@import url('default.css');

body {
    margin:  0px;
    padding: 0px;
}

.filetree {
    font-size: 0.8em;
}

#db-header {
    height: 80px;
}

#db-footer {
    height: 1px;
}
CSS
        );

        file_put_contents(
            $path . DIRECTORY_SEPARATOR . 'index.xsl',
            <<<XML
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />

  <xsl:template match="/">
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
      <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <title></title>

        <link rel="stylesheet" href="{\$root}css/template.css" type="text/css" />
        <script type="text/javascript" src="{\$root}js/jquery-1.4.2.min.js"></script>
        <script type="text/javascript" src="{\$root}js/jquery-ui-1.8.2.custom.min.js"></script>
        <script type="text/javascript" src="{\$root}js/jquery.cookie.js"></script>
        <script type="text/javascript" src="{\$root}js/jquery.treeview.js"></script>
      </head>
      <body>
        <table id="page">
          <tr><td colspan="2" id="db-header"></td></tr>
          <tr>
            <td id="sidebar">
              <iframe name="nav" id="nav" src="{\$root}nav.html" />
            </td>
            <td id="contents">
              <iframe name="content" id="content" src="{\$root}content.html" />
            </td>
          </tr>
          <tr><td colspan="2" id="db-footer"></td></tr>
        </table>
      </body>
    </html>
  </xsl:template>

</xsl:stylesheet>
XML
        );

        $name    = $this->getName();
        $author  = $this->getAuthor();
        $version = $this->getVersion();

        file_put_contents(
            $path . DIRECTORY_SEPARATOR . 'template.xml',
            <<<XML
<?xml version="1.0" encoding="utf-8"?>

<template>
  <author>$author</author>
  <version>$version</version>
  <copyright />
  <transformations>
    <transformation query="copy" writer="FileIo" source="js" artifact="js"/>
    <transformation query="copy" writer="FileIo" source="images" artifact="images"/>
    <transformation query="copy" writer="FileIo" source="templates/new_black/css" artifact="css"/>
    <transformation query="copy" writer="FileIo" source="templates/cache/$name/css" artifact="css"/>
    <transformation query="copy" writer="FileIo" source="templates/new_black/images" artifact="images"/>
    <transformation query="" writer="xsl" source="templates/cache/$name/index.xsl" artifact="index.html"/>
    <transformation query="" writer="xsl" source="templates/new_black/sidebar.xsl" artifact="nav.html"/>
    <transformation query="/project/file/@path" writer="xsl" source="templates/new_black/api-doc.xsl" artifact="{\$path}"/>
  </transformations>
</template>
XML
        );

        echo 'Finished generating a new template at: ' . $path . PHP_EOL . PHP_EOL;
    }

}