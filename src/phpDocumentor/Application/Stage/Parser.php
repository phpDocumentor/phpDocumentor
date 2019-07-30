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

namespace phpDocumentor\Application\Stage;

use Exception;
use InvalidArgumentException;
use phpDocumentor\Descriptor\Cache\ProjectDescriptorMapper;
use phpDocumentor\Descriptor\Collection as PartialsCollection;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Parser\FileCollector;
use phpDocumentor\Parser\Parser as DocParser;
use phpDocumentor\Reflection\DocBlock\ExampleFinder;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Zend\Cache\Storage\StorageInterface;

/**
 * Parses the given source code and creates a structure file.
 *
 * The parse task uses the source files defined either by -f or -d options and
 * generates a structure file (structure.xml) at the target location (which is
 * the folder 'output' unless the option -t is provided).
 */
final class Parser
{
    /** @var ProjectDescriptorBuilder $builder */
    private $builder;

    /** @var DocParser $parser */
    private $parser;

    /** @var StorageInterface */
    private $cache;

    /**
     * @var ExampleFinder
     */
    private $exampleFinder;

    /**
     * @var PartialsCollection
     */
    private $partials;

    /**
     * @var FileCollector
     */
    private $fileCollector;

    private $logger;

    /**
     * ParseCommand constructor.
     */
    public function __construct(
        ProjectDescriptorBuilder $builder,
        DocParser $parser,
        FileCollector $fileCollector,
        StorageInterface $cache,
        ExampleFinder $exampleFinder,
        PartialsCollection $partials,
        LoggerInterface $logger
    ) {
        $this->builder = $builder;
        $this->parser = $parser;
        $this->cache = $cache;
        $this->exampleFinder = $exampleFinder;
        $this->partials = $partials;
        $this->fileCollector = $fileCollector;
        $this->logger = $logger;
    }

    private function getBuilder(): ProjectDescriptorBuilder
    {
        return $this->builder;
    }

    private function getParser(): DocParser
    {
        return $this->parser;
    }

    /**
     * Returns the Cache.
     *
     * @throws InvalidArgumentException
     */
    private function getCache(): StorageInterface
    {
        return $this->cache;
    }

    /**
     * Executes the business logic involved with this command.
     *
     * @throws Exception if the target location is not a folder.
     */
    public function __invoke(array $configuration): array
    {
        //Grep only the first version for now. Multi version support will be added later
        $version = current($configuration['phpdocumentor']['versions']);

        //We are currently in the parser stage so grep the api config.
        //And for now we support a single api definition. Could be more in the future.
        $apiConfig = $version['api'][0];

        $parser = $this->getParser();
        $parser->setForced(!$configuration['phpdocumentor']['use-cache']);
        $parser->setEncoding($apiConfig['encoding']);
        $parser->setMarkers($apiConfig['markers']);
        $parser->setIgnoredTags($apiConfig['ignore-tags']);
        $parser->setValidate($apiConfig['validate']);
        $parser->setDefaultPackageName($apiConfig['default-package-name']);

        $builder = $this->getBuilder();
        $builder->createProjectDescriptor();
        $projectDescriptor = $builder->getProjectDescriptor();
        $projectDescriptor->setName($configuration['phpdocumentor']['title']);
        $projectDescriptor->setPartials($this->partials);

        $mapper = new ProjectDescriptorMapper($this->getCache());

        if ($configuration['phpdocumentor']['use-cache']) {
            //TODO: Re-enable garbage collection here.
            //$mapper->garbageCollect($files);
            $mapper->populate($projectDescriptor);
        }

        // must be below the mapper populating the project descriptor because overriding the visibility or source
        // can trigger a rebuild; but this detection only works if the settings were loaded from cache
        $visibility = $this->getVisibility($apiConfig);
        $projectDescriptor->getSettings()->setVisibility($visibility);
        $projectDescriptor->getSettings()->setMarkers($apiConfig['markers']);
        if ($apiConfig['include-source']) {
            $projectDescriptor->getSettings()->includeSource();
        } else {
            $projectDescriptor->getSettings()->excludeSource();
        }

        //TODO: Should determine root based on filesystems. Could be an issue for multiple.
        // Need some config update here.
        $this->exampleFinder->setSourceDirectory(getcwd());
        $this->exampleFinder->setExampleDirectories(['.']);

        $this->log('Collecting files .. ');
        $files = $this->getFileCollection($apiConfig);
        $this->log('OK');

        $this->log('Parsing files', LogLevel::NOTICE);
        $parser->parse($builder, $files);

        $this->log('Storing cache .. ', LogLevel::NOTICE);
        $projectDescriptor->getSettings()->clearModifiedFlag();
        $mapper->save($projectDescriptor);
        $this->log('OK');

        return $configuration;
    }

    /**
     * Returns the collection of files based on the input and configuration.
     */
    private function getFileCollection(array $apiConfig): array
    {
        $ignorePaths = array_map(
            function ($value) {
                if (substr((string) $value, -1) === '*') {
                    return substr($value, 0, -1);
                }

                return $value;
            },
            $apiConfig['ignore']['paths']
        );

        return $this->fileCollector->getFiles(
            $apiConfig['source']['dsn'],
            $apiConfig['source']['paths'],
            [
                'paths' => $ignorePaths,
                'hidden' => $apiConfig['ignore']['hidden'],
            ],
            $apiConfig['extensions']
        );
    }

    private function getVisibility(array $apiConfig): ?int
    {
        $visibilities = $apiConfig['visibility'];
        $visibility = null;
        foreach ($visibilities as $item) {
            switch ($item) {
                case 'public':
                    $visibility |= ProjectDescriptor\Settings::VISIBILITY_PUBLIC;
                    break;
                case 'protected':
                    $visibility |= ProjectDescriptor\Settings::VISIBILITY_PROTECTED;
                    break;
                case 'private':
                    $visibility |= ProjectDescriptor\Settings::VISIBILITY_PRIVATE;
                    break;
            }
        }
        return $visibility;
    }

    /**
     * Dispatches a logging request.
     *
     * @param string   $priority The logging priority as declared in the LogLevel PSR-3 class.
     * @param string[] $parameters
     */
    private function log(string $message, string $priority = LogLevel::INFO, array $parameters = []): void
    {
        $this->logger->log($priority, $message, $parameters);
    }
}
