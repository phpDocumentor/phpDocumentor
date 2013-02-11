<?php

namespace phpDocumentor\Transformer\Template;

use phpDocumentor\Transformer\Template;
use phpDocumentor\Transformer\Transformer;
use phpDocumentor\Transformer\Writer\Collection;

class Factory
{
    protected $templatesPath = 'data/templates';

    /** @var \phpDocumentor\Transformer\Writer\Collection */
    protected $writers;

    public function __construct($templatesPath, Collection $writers)
    {
        $this->templatesPath = $templatesPath;
        $this->writers       = $writers;
    }

    /**
     * @param string $nameOrPath
     *
     * @throws \InvalidArgumentException
     *
     * @return Template
     */
    public function create($nameOrPath, Transformer $transformer)
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
            $cachePath = rtrim($this->getTemplatesPath(), '/\\') . DIRECTORY_SEPARATOR . $templateNamePart;

            // move the files to a cache location and then change the path
            // variable to match the new location
            $this->copyRecursive($path, $cachePath);
            $path = $cachePath;

            // transform all directory separators to underscores and lowercase
            $nameOrPath = strtolower(
                str_replace(DIRECTORY_SEPARATOR, '_', rtrim($nameOrPath, DIRECTORY_SEPARATOR))
            );
        }

        // if we load a default template
        if ($path === null) {
            $path = rtrim($this->getTemplatesPath(), '/\\') . DIRECTORY_SEPARATOR . $nameOrPath;
        }

        if (!file_exists($path) || !is_readable($path)) {
            throw new \InvalidArgumentException(
                'The given template ' . $nameOrPath.' could not be found or is not readable'
            );
        }

        // track templates to be able to refer to them later
        $template =  new Template($nameOrPath, $path);

        $loader = new Template\XmlLoader($transformer, $this->writers);
        $loader->load($template, file_get_contents($path  . DIRECTORY_SEPARATOR . 'template.xml'));
        return $template;
    }

    public function getTemplatesPath()
    {
        return $this->templatesPath;
    }

    /**
     * Copies a file or folder recursively to another location.
     *
     * @param string $src The source location to copy
     * @param string $dst The destination location to copy to
     *
     * @throws \Exception if $src does not exist or $dst is not writable
     *
     * @return void
     */
    public function copyRecursive($src, $dst)
    {
        // if $src is a normal file we can do a regular copy action
        if (is_file($src)) {
            copy($src, $dst);
            return;
        }

        $dir = opendir($src);
        if (!$dir) {
            throw new \Exception('Unable to locate path "' . $src . '"');
        }

        // check if the folder exists, otherwise create it
        if ((!file_exists($dst)) && (false === mkdir($dst))) {
            throw new \Exception('Unable to create folder "' . $dst . '"');
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
