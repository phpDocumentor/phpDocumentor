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

class Factory
{
    const TEMPLATE_DEFINITION_FILENAME = 'template.xml';

    /** @var Serializer */
    private $serializer;

    /** @var PathResolver */
    private $pathResolver;

    /**
     * Constructs a new template factory with its dependencies.
     *
     * @param PathResolver $pathResolver
     * @param Serializer   $serializer   Serializer used to convert the XML files to models.
     */
    public function __construct(PathResolver $pathResolver, Serializer $serializer)
    {
        $this->serializer   = $serializer;
        $this->pathResolver = $pathResolver;
    }

    /**
     * Attempts to find, construct and return a template object with the given template name or (relative/absolute)
     * path.
     *
     * @param string $nameOrPath
     *
     * @return Template
     */
    public function get($nameOrPath)
    {
        return $this->createTemplateFromXml(
            $this->fetchTemplateXmlFromPath(
                $this->pathResolver->resolve($nameOrPath)
            )
        );
    }

    /**
     * Returns a list of all template names.
     *
     * @return string[]
     */
    public function getAllNames()
    {
        /** @var \RecursiveDirectoryIterator $files */
        $files = new \DirectoryIterator($this->getTemplatePath());

        $template_names = array();
        while ($files->valid()) {
            $name = $files->getBasename();

            // skip abstract files
            if (!$files->isDir() || in_array($name, array('.', '..'))) {
                $files->next();
                continue;
            }

            $template_names[] = $name;
            $files->next();
        }

        return $template_names;
    }

    /**
     * Returns the path where all templates are stored.
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return $this->pathResolver->getTemplatePath();
    }

    /**
     * Loads the template definition file from the given path and returns it's contents.
     *
     * @param string $path
     *
     * @return string
     */
    protected function fetchTemplateXmlFromPath($path)
    {
        return file_get_contents($path . DIRECTORY_SEPARATOR . self::TEMPLATE_DEFINITION_FILENAME);
    }

    /**
     * Creates and returns a template object based on the provided template definition.
     *
     * @param string $xml
     *
     * @return Template
     */
    protected function createTemplateFromXml($xml)
    {
        /** @var Template $template */
        $template = $this->serializer->deserialize($xml, 'phpDocumentor\Transformer\Template', 'xml');
        $template->propagateParameters();

        return $template;
    }
}
