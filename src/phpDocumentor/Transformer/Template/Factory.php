<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Transformer\Template;

use DirectoryIterator;
use InvalidArgumentException;
use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\MountManager;
use phpDocumentor\Dsn;
use phpDocumentor\Parser\FlySystemFactory;
use phpDocumentor\Path;
use phpDocumentor\Transformer\Template;
use phpDocumentor\Transformer\Transformation;
use phpDocumentor\Transformer\Writer\Collection as WriterCollection;
use RecursiveDirectoryIterator;
use RuntimeException;
use SimpleXMLElement;
use Symfony\Component\Stopwatch\Stopwatch;
use Webmozart\Assert\Assert;

use function array_merge;
use function file_exists;
use function in_array;
use function is_readable;
use function rtrim;

use const DIRECTORY_SEPARATOR;

/**
 * @todo:
 * - add logic to load templates from the global templates folder (create template objects)
 * - add logic to load template from a path
 * - add logic to register a template from a DNS and load it.
 */
class Factory
{
    final public const TEMPLATE_DEFINITION_FILENAME = 'template.xml';
    private array $templateFileSystemPrefixes = ['templates'];
    private MountManager $templateFileSystems;

    /**
     * Constructs a new template factory with its dependencies.
     */
    public function __construct(
        private readonly WriterCollection $writerCollection,
        private readonly FlySystemFactory $flySystemFactory,
        private readonly string $globalTemplatesPath,
    ) {
        $this->templateFileSystems = new MountManager([
            'templates' => $this->getTemplatesDirectory(),
        ]);
    }

    /**
     * Attempts to find, construct and return a template object with the given template name or (relative/absolute)
     * path.
     *
     * @param array<int, array{name:string, location: ?Path, parameters:array<string, string>}> $templates
     */
    public function getTemplates(array $templates): Collection
    {
        $stopWatch = new Stopwatch();
        $loadedTemplates = [];

        foreach ($templates as $template) {
            $stopWatch->start('load template');

            $location = $template['location'] ?? null;
            $templateNameOrLocation = $location instanceof Path
                ? ($location . '/' . $template['name'])
                : $template['name'];

            $loadedTemplates[$template['name']] = $this->loadTemplate(
                $templateNameOrLocation,
                $template['parameters'] ?? [],
            );
            $stopWatch->stop('load template');
        }

        return new Collection($loadedTemplates);
    }

    /** @param array<string, string> $parameters */
    private function loadTemplate(string $template, array $parameters): Template
    {
        $template = $this->createTemplateFromXml($template, $parameters);

        /** @var Transformation $transformation */
        foreach ($template as $transformation) {
            $writer = $this->writerCollection->get($transformation->getWriter());
            $writer->checkRequirements();
        }

        return $template;
    }

    /**
     * Returns a list of all template names.
     *
     * @return string[]
     */
    public function getAllNames(): array
    {
        $templateNames = [];
        foreach ($this->templateFileSystemPrefixes as $prefix) {
            foreach ($this->templateFileSystems->listContents($prefix . '://') as $file) {
                if ($file['type'] !== 'dir') {
                    continue;
                }

                $templateNames[] = $file['basename'];
            }
        }

        return $templateNames;
    }

    /** @return Template[] */
    private function getAllTemplates(): array
    {
        $templates = [];
        foreach ($this->templateFileSystemPrefixes as $prefix) {
            foreach ($this->templateFileSystems->listContents($prefix . '://') as $file) {
                if ($file['type'] !== 'dir') {
                    continue;
                }

                $templates[] = $file['basename'];
            }
        }

        return $templates;
    }

    public function registerTemplate(string $prefix, Dsn $dsn): void
    {
        $this->templateFileSystemPrefixes[] = $prefix;
        $this->templateFileSystems->mountFilesystem($prefix, $this->flySystemFactory->create($dsn));
    }

    /**
     * Returns the path where all templates are stored.
     */
    public function getTemplatesPath(): string
    {
        return $this->globalTemplatesPath;
    }

    /**
     * Creates and returns a template object based on the provided template definition.
     *
     * @param array<string, string> $templateParams
     */
    private function createTemplateFromXml(
        string $nameOrPath,
        array $templateParams,
    ): Template {
        $this->templateFileSystems->mountFilesystem('template', $this->resolve($nameOrPath));

        $xml = $this->templateFileSystems->read('template://' . self::TEMPLATE_DEFINITION_FILENAME);
        Assert::string($xml);

        return $this->createTemplateFromString($xml, $templateParams);
    }

    private function resolve(string $nameOrPath): FilesystemInterface
    {
        $configPath = rtrim($nameOrPath, DIRECTORY_SEPARATOR) . '/template.xml';
        if (file_exists($configPath) && is_readable($configPath)) {
            return $this->flySystemFactory->create(Dsn::createFromString(rtrim($nameOrPath, DIRECTORY_SEPARATOR)));
        }

        // if we load a global template
        $globalTemplatesFilesystem = $this->getTemplatesDirectory();
        if ($globalTemplatesFilesystem->has($nameOrPath)) {
            $templateFilesystem = $this->createNewFilesystemFromSubfolder($globalTemplatesFilesystem, $nameOrPath);

            if (! $templateFilesystem->has('template.xml')) {
                throw new TemplateNotFound($nameOrPath);
            }

            return $templateFilesystem;
        }

        throw new TemplateNotFound($nameOrPath);
    }

    private function getTemplatesDirectory(): Filesystem
    {
        $dsnString = $this->getTemplatesPath();
        try {
            $filesystem = $this->flySystemFactory->create(Dsn::createFromString($dsnString));
        } catch (InvalidArgumentException) {
            throw new RuntimeException(
                'Unable to access the folder with the global templates, received DSN is: ' . $dsnString,
            );
        }

        return $filesystem;
    }

    private function createNewFilesystemFromSubfolder(
        Filesystem $hostFilesystem,
        string $subfolder,
    ): Filesystem {
        $hostFilesystemAdapter = $hostFilesystem->getAdapter();
        if (! $hostFilesystemAdapter instanceof AbstractAdapter) {
            throw new RuntimeException(
                'Failed to load template, The filesystem of the global templates does not support '
                . 'getting a subfolder from it',
            );
        }

        $templateAdapter = clone $hostFilesystemAdapter;
        $globalRoot = $templateAdapter->getPathPrefix();
        $templateAdapter->setPathPrefix($globalRoot . $subfolder);

        return new Filesystem($templateAdapter);
    }

    private function createTemplateFromString(bool|string $xml, array $templateParams): Template
    {
        $xml = new SimpleXMLElement($xml);
        $template = new Template((string)$xml->name, $this->templateFileSystems);
        $template->setAuthor((string)$xml->author . ((string)$xml->email ? ' <' . $xml->email . '>' : ''));
        $template->setVersion((string)$xml->version);
        $template->setCopyright((string)$xml->copyright);
        $template->setDescription((string)$xml->description);

        if ($xml->parameters) {
            foreach ($xml->parameters->children() as $parameter) {
                $parameterObject = new Parameter((string)$parameter->attributes()->key, (string)$parameter);
                $template->setParameter($parameterObject->key(), $parameterObject);
            }
        }

        foreach ($templateParams as $key => $value) {
            $parameterObject = new Parameter($key, $value);
            $template->setParameter($parameterObject->key(), $parameterObject);
        }

        $i = 0;
        foreach ($xml->transformations->transformation as $transformation) {
            $transformationObject = new Transformation(
                $template,
                (string)$transformation->attributes()->query,
                (string)$transformation->attributes()->writer,
                (string)$transformation->attributes()->source,
                (string)$transformation->attributes()->artifact,
            );
            $parameters = [];
            foreach ($transformation->parameter as $parameter) {
                $parameterObject = new Parameter((string)$parameter->attributes()->key, (string)$parameter);
                $parameters[$parameterObject->key()] = $parameterObject;
            }

            $transformationObject->setParameters(array_merge($parameters, $template->getParameters()));

            $template[$i++] = $transformationObject;
        }

        $template->propagateParameters();

        return $template;
    }
}
