<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Template;

use InvalidArgumentException;
use Symfony\Component\Filesystem\Filesystem;
use const DIRECTORY_SEPARATOR;
use function basename;
use function file_exists;
use function is_readable;
use function rtrim;

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
            $path             = rtrim($nameOrPath, DIRECTORY_SEPARATOR);
            $templateNamePart = basename($path);
            $cachePath        = rtrim($this->templatePath, '/\\') . DIRECTORY_SEPARATOR . $templateNamePart;

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
            throw new InvalidArgumentException(
                'The given template ' . $nameOrPath . ' could not be found or is not readable'
            );
        }

        return $path;
    }

    /**
     * Returns the path where all templates are stored.
     */
    public function getTemplatePath() : string
    {
        return $this->templatePath;
    }
}
