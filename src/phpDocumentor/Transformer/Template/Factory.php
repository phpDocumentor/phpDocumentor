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

use phpDocumentor\Transformer\Template;
use phpDocumentor\Transformer\Transformation;

class Factory
{
    const TEMPLATE_DEFINITION_FILENAME = 'template.xml';

    /** @var PathResolver */
    private $pathResolver;

    /**
     * Constructs a new template factory with its dependencies.
     */
    public function __construct(PathResolver $pathResolver)
    {
        $this->pathResolver = $pathResolver;
    }

    /**
     * Attempts to find, construct and return a template object with the given template name or (relative/absolute)
     * path.
     */
    public function get(string $nameOrPath): Template
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
    public function getAllNames(): array
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
     */
    public function getTemplatePath(): string
    {
        return $this->pathResolver->getTemplatePath();
    }

    /**
     * Loads the template definition file from the given path and returns it's contents.
     */
    protected function fetchTemplateXmlFromPath(string $path): string
    {
        return file_get_contents($path . DIRECTORY_SEPARATOR . self::TEMPLATE_DEFINITION_FILENAME);
    }

    /**
     * Creates and returns a template object based on the provided template definition.
     */
    protected function createTemplateFromXml(string $xml): Template
    {
        $xml = new \SimpleXMLElement($xml);
        $template = new Template((string) $xml->name);
        $template->setAuthor((string) $xml->author . ((string)$xml->email ? ' <' . $xml->email . '>' : ''));
        $template->setVersion((string) $xml->version);
        $template->setCopyright((string) $xml->copyright);
        $template->setDescription((string) $xml->description);
        foreach ($xml->parameter as $parameter) {
            $parameterObject = new Parameter();
            $parameterObject->setKey((string) $parameter->attributes()->key);
            $parameterObject->setValue((string) $parameter);
            $template->setParameter($parameterObject->getKey(), $parameterObject);
        }
        $i = 0;
        foreach ($xml->transformations->transformation as $transformation) {
            $transformationObject = new Transformation(
                (string) $transformation->attributes()->query,
                (string) $transformation->attributes()->writer,
                (string) $transformation->attributes()->source,
                (string) $transformation->attributes()->artifact
            );
            $parameters = [];
            foreach ($transformation->parameter as $parameter) {
                $parameterObject = new Parameter();
                $parameterObject->setKey((string) $parameter->attributes()->key);
                $parameterObject->setValue((string) $parameter);
                $parameters[$parameterObject->getKey()] = $parameterObject;
            }
            $transformationObject->setParameters($parameters);

            $template[$i++] = $transformationObject;
        }
        $template->propagateParameters();

        return $template;
    }
}
