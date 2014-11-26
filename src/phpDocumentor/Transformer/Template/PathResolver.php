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

namespace phpDocumentor\Transformer\Template;

use Symfony\Component\Filesystem\Filesystem;

class PathResolver
{
    private $templatePath;

    public function __construct($templatePath)
    {
        $this->templatePath = $templatePath;
    }

    public function resolve($nameOrPath)
    {
        $path = null;

        // if this is an absolute path; load the template into the configuration
        // Please note that this _could_ override an existing template when
        // you have a template in a subfolder with the same name as a default
        // template; we have left this in on purpose to allow people to override
        // templates should they choose to.
        $configPath = rtrim($nameOrPath, DIRECTORY_SEPARATOR) . '/template.xml';
        if (file_exists($configPath) && is_readable($configPath)) {
            $path = rtrim($nameOrPath, DIRECTORY_SEPARATOR);
            $templateNamePart = basename($path);
            $cachePath = rtrim($this->templatePath, '/\\') . DIRECTORY_SEPARATOR . $templateNamePart;

            // move the files to a cache location and then change the path
            // variable to match the new location
            $filesystem = new Filesystem();
            $filesystem->mirror($path, $cachePath);
            $path = $cachePath;
        }

        // if we load a default template
        if ($path === null) {
            $path = rtrim($this->templatePath, '/\\') . DIRECTORY_SEPARATOR . $nameOrPath;
        }

        if (!file_exists($path) || !is_readable($path)) {
            throw new \InvalidArgumentException(
                'The given template ' . $nameOrPath . ' could not be found or is not readable'
            );
        }

        return $path;
    }

    /**
     * Returns the path where all templates are stored.
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return $this->templatePath;
    }
}
