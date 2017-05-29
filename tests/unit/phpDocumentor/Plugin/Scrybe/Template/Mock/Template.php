<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Scrybe\Template\Mock;

use phpDocumentor\Plugin\Scrybe\Template\TemplateInterface;

/**
 * Mock object for templates.
 */
class Template implements TemplateInterface
{
    /**
     * @param string $templatePath
     */
    public function __construct($templatePath)
    {
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
    }

    /**
     * @param string $extension
     */
    public function setExtension($extension)
    {
    }

    /**
     * @param string $contents
     * @param array  $options
     */
    public function decorate($contents, array $options = array())
    {
    }

    public function getAssets()
    {
    }
}
