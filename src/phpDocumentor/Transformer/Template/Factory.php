<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Template;

use JMS\Serializer\SerializerInterface;
use phpDocumentor\Transformer\Template;

class Factory
{
    const TEMPLATE_DEFINITION_FILENAME = 'template.xml';

    /** @var SerializerInterface */
    private $serializer;

    /** @var PathResolver */
    private $pathResolver;

    /**
     * Constructs a new template factory with its dependencies.
     *
     * @param SerializerInterface $serializer Serializer used to convert the XML files to models.
     */
    public function __construct(PathResolver $pathResolver, SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
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

        $template_names = [];
        while ($files->valid()) {
            $name = $files->getBasename();

            // skip abstract files
            if (!$files->isDir() || in_array($name, ['.', '..'], true)) {
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
