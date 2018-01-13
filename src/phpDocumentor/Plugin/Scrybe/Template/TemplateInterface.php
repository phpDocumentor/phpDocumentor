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

namespace phpDocumentor\Plugin\Scrybe\Template;

interface TemplateInterface
{
    public function __construct($templatePath);

    public function setName($name);

    public function setPath($path);

    public function setExtension($extension);

    public function decorate($contents, array $options = []);

    public function getAssets();
}
