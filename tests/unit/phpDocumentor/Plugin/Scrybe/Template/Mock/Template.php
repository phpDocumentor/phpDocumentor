<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
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
    public function __construct($templatePath)
    {
    }

    public function setName($name)
    {
    }

    public function setPath($path)
    {
    }

    public function setExtension($extension)
    {
    }

    public function decorate($contents, array $options = [])
    {
    }

    public function getAssets()
    {
    }
}
