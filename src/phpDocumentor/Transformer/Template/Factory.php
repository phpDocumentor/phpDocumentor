<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Template;

use DirectoryIterator;
use League\Flysystem\MountManager;
use phpDocumentor\Dsn;
use phpDocumentor\Parser\FlySystemFactory;
use phpDocumentor\Transformer\Template;
use phpDocumentor\Transformer\Transformation;
use phpDocumentor\Transformer\Transformer;
use RecursiveDirectoryIterator;
use SimpleXMLElement;
use const DIRECTORY_SEPARATOR;
use function file_exists;
use function in_array;
use function is_readable;
use function rtrim;

class Factory
{
    public const TEMPLATE_DEFINITION_FILENAME = 'template.xml';

    /** @var FlySystemFactory */
    private $flySystemFactory;

    /** @var string */
    private $globalTemplatesPath;

    /**
     * Constructs a new template factory with its dependencies.
     */
    public function __construct(
        FlySystemFactory $flySystemFactory,
        string $globalTemplatesPath
    ) {
        $this->flySystemFactory = $flySystemFactory;
        $this->globalTemplatesPath = $globalTemplatesPath;
    }

    /**
     * Attempts to find, construct and return a template object with the given template name or (relative/absolute)
     * path.
     */
    public function get(Transformer $transformer, string $nameOrPath) : Template
    {
        return $this->createTemplateFromXml($transformer, $nameOrPath);
    }

    /**
     * Returns a list of all template names.
     *
     * @return string[]
     */
    public function getAllNames() : array
    {
        /** @var RecursiveDirectoryIterator $files */
        $files = new DirectoryIterator($this->getTemplatesPath());

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
    public function getTemplatesPath() : string
    {
        return $this->globalTemplatesPath;
    }

    /**
     * Creates and returns a template object based on the provided template definition.
     */
    protected function createTemplateFromXml(Transformer $transformer, string $nameOrPath) : Template
    {
        $path = $this->resolve($nameOrPath);

        // create the filesystems that a template needs to be able to manipulate, the source folder containing this
        // template its files; the destination to where it can write its files and a global templates folder where to
        // get global template files from
        $files = new MountManager(
            [
                'templates' => $transformer->getTemplatesDirectory(),
                'template' => $this->flySystemFactory->create(new Dsn($path)),
                'destination' => $transformer->destination(),
            ]
        );

        $xml = $files->read('template://' . self::TEMPLATE_DEFINITION_FILENAME);

        $xml = new SimpleXMLElement($xml);
        $template = new Template((string) $xml->name, $files);
        $template->setAuthor((string) $xml->author . ((string) $xml->email ? ' <' . $xml->email . '>' : ''));
        $template->setVersion((string) $xml->version);
        $template->setCopyright((string) $xml->copyright);
        $template->setDescription((string) $xml->description);
        foreach ($xml->parameter as $parameter) {
            $parameterObject = new Parameter((string) $parameter->attributes()->key, (string) $parameter);
            $template->setParameter($parameterObject->key(), $parameterObject);
        }
        $i = 0;
        foreach ($xml->transformations->transformation as $transformation) {
            $transformationObject = new Transformation(
                $template,
                (string) $transformation->attributes()->query,
                (string) $transformation->attributes()->writer,
                (string) $transformation->attributes()->source,
                (string) $transformation->attributes()->artifact
            );
            $parameters = [];
            foreach ($transformation->parameter as $parameter) {
                $parameterObject = new Parameter((string) $parameter->attributes()->key, (string) $parameter);
                $parameters[$parameterObject->key()] = $parameterObject;
            }
            $transformationObject->setParameters($parameters);

            $template[$i++] = $transformationObject;
        }
        $template->propagateParameters();

        return $template;
    }

    private function resolve(string $nameOrPath) : string
    {
        $path = null;

        $configPath = rtrim($nameOrPath, DIRECTORY_SEPARATOR) . '/template.xml';
        if (file_exists($configPath) && is_readable($configPath)) {
            $path = rtrim($nameOrPath, DIRECTORY_SEPARATOR);
        }

        // if we load a default template
        if ($path === null) {
            $path = rtrim($this->getTemplatesPath(), '/\\') . DIRECTORY_SEPARATOR . $nameOrPath;
        }

        if (!file_exists($path) || !is_readable($path)) {
            throw new TemplateNotFound($path);
        }

        return $path;
    }
}
