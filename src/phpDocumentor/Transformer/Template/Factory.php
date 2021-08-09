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
use phpDocumentor\Transformer\Template;
use phpDocumentor\Transformer\Transformation;
use phpDocumentor\Transformer\Writer\Collection as WriterCollection;
use RecursiveDirectoryIterator;
use RuntimeException;
use SimpleXMLElement;
use Symfony\Component\Stopwatch\Stopwatch;

use function array_merge;
use function file_exists;
use function in_array;
use function is_readable;
use function rtrim;

use const DIRECTORY_SEPARATOR;

class Factory
{
    public const TEMPLATE_DEFINITION_FILENAME = 'template.xml';

    /** @var FlySystemFactory */
    private $flySystemFactory;

    /** @var string */
    private $globalTemplatesPath;

    /** @var WriterCollection */
    private $writerCollection;

    /**
     * Constructs a new template factory with its dependencies.
     */
    public function __construct(
        WriterCollection $writerCollection,
        FlySystemFactory $flySystemFactory,
        string $globalTemplatesPath
    ) {
        $this->flySystemFactory = $flySystemFactory;
        $this->globalTemplatesPath = $globalTemplatesPath;
        $this->writerCollection = $writerCollection;
    }

    /**
     * Attempts to find, construct and return a template object with the given template name or (relative/absolute)
     * path.
     *
     * @param array<int, array{name:string, parameters:array<string, string>}> $templates
     */
    public function getTemplates(array $templates, FilesystemInterface $output): Collection
    {
        $stopWatch = new Stopwatch();
        $loadedTemplates = [];

        foreach ($templates as $template) {
            $stopWatch->start('load template');
            $loadedTemplates[$template['name']] = $this->loadTemplate(
                $output,
                $template['name'],
                $template['parameters'] ?? []
            );
            $stopWatch->stop('load template');
        }

        return new Collection($loadedTemplates);
    }

    /**
     * @param array<string, string> $parameters
     */
    private function loadTemplate(FilesystemInterface $output, string $template, array $parameters): Template
    {
        $template = $this->createTemplateFromXml($output, $template, $parameters);

        /** @var Transformation $transformation */
        foreach ($template as $transformation) {
            $writer = $this->writerCollection[$transformation->getWriter()];
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
        /** @var RecursiveDirectoryIterator $files */
        $files = new DirectoryIterator($this->getTemplatesPath());

        $templateNames = [];
        while ($files->valid()) {
            $name = $files->getBasename();

            // skip abstract files
            if (!$files->isDir() || in_array($name, ['.', '..'], true)) {
                $files->next();
                continue;
            }

            $templateNames[] = $name;
            $files->next();
        }

        return $templateNames;
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
        FilesystemInterface $filesystem,
        string $nameOrPath,
        array $templateParams
    ): Template {
        // create the filesystems that a template needs to be able to manipulate, the source folder containing this
        // template its files; the destination to where it can write its files and a global templates folder where to
        // get global template files from
        $files = new MountManager(
            [
                'templates' => $this->getTemplatesDirectory(),
                'template' => $this->resolve($nameOrPath),
                'destination' => $filesystem,
            ]
        );

        $xml = $files->read('template://' . self::TEMPLATE_DEFINITION_FILENAME);

        $xml = new SimpleXMLElement($xml);
        $template = new Template((string) $xml->name, $files);
        $template->setAuthor((string) $xml->author . ((string) $xml->email ? ' <' . $xml->email . '>' : ''));
        $template->setVersion((string) $xml->version);
        $template->setCopyright((string) $xml->copyright);
        $template->setDescription((string) $xml->description);

        if ($xml->parameters) {
            foreach ($xml->parameters->children() as $parameter) {
                $parameterObject = new Parameter((string) $parameter->attributes()->key, (string) $parameter);
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

            $transformationObject->setParameters(array_merge($parameters, $template->getParameters()));

            $template[$i++] = $transformationObject;
        }

        $template->propagateParameters();

        return $template;
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

            if (!$templateFilesystem->has('template.xml')) {
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
        } catch (InvalidArgumentException $e) {
            throw new RuntimeException(
                'Unable to access the folder with the global templates, received DSN is: ' . $dsnString
            );
        }

        return $filesystem;
    }

    private function createNewFilesystemFromSubfolder(
        Filesystem $hostFilesystem,
        string $subfolder
    ): Filesystem {
        $hostFilesystemAdapter = $hostFilesystem->getAdapter();
        if (!$hostFilesystemAdapter instanceof AbstractAdapter) {
            throw new RuntimeException(
                'Failed to load template, The filesystem of the global templates does not support '
                . 'getting a subfolder from it'
            );
        }

        $templateAdapter = clone $hostFilesystemAdapter;
        $globalRoot = $templateAdapter->getPathPrefix();
        $templateAdapter->setPathPrefix($globalRoot . $subfolder);

        return new Filesystem($templateAdapter);
    }
}
