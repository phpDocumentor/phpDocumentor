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

use JMS\Serializer\Serializer;
use phpDocumentor\Transformer\Template;
use phpDocumentor\Transformer\Transformation;

/**
 * Contains a collection of Templates that may be queried.
 */
class Collection extends \ArrayObject
{
    protected $templatesPath = 'data/templates';

    /** @var Serializer $serializer */
    protected $serializer;

    public function __construct($templatesPath, Serializer $serializer)
    {
        $this->templatesPath = $templatesPath;
        $this->serializer    = $serializer;
    }

    /**
     * Returns a list of all transformations contained in the templates of this collection.
     *
     * @return Transformation[]
     */
    public function getTransformations()
    {
        $result = array();
        foreach ($this as $template) {
            foreach ($template as $transformation) {
                $result[] = $transformation;
            }
        }

        return $result;
    }

    /**
     * @param string $nameOrPath
     *
     * @throws \InvalidArgumentException
     *
     * @return Template
     */
    public function load($nameOrPath)
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
            $filesystem = new \Symfony\Component\Filesystem\Filesystem;
            $filesystem->mirror($path, $cachePath);
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

        /** @var Template $template  */
        $template = $this->getSerializer()->deserialize(
            file_get_contents($path . DIRECTORY_SEPARATOR . 'template.xml'),
            'phpDocumentor\Transformer\Template',
            'xml'
        );
        $template->propagateParameters();

        $this[$template->getName()] = $template;
    }

    /**
     * @return string
     */
    public function getTemplatesPath()
    {
        return $this->templatesPath;
    }

    /**
     * @return \JMS\Serializer\Serializer
     */
    public function getSerializer()
    {
        return $this->serializer;
    }
}
