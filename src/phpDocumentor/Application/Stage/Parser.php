<?php
/**
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @copyright 2010-2017 Mike van Riel<mike@phpdoc.org>
 *  @license   http://www.opensource.org/licenses/mit-license.php MIT
 *  @link      http://phpdoc.org
 */

namespace phpDocumentor\Application\Stage;

use phpDocumentor\Descriptor\Cache\ProjectDescriptorMapper;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\DomainModel\Parser\FileCollector;
use phpDocumentor\Event\LogEvent;
use phpDocumentor\Parser\Parser as DocParser;
use phpDocumentor\Partials\Collection as PartialsCollection;
use phpDocumentor\Reflection\DocBlock\ExampleFinder;
use Psr\Log\LogLevel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;
use Zend\Cache\Storage\StorageInterface;
use Zend\I18n\Translator\Translator;

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
    protected $builder;

    /** @var DocParser $parser */
    protected $parser;

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

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * ParseCommand constructor.
     * @param ProjectDescriptorBuilder $builder
     * @param DocParser $parser
     * @param FileCollector $fileCollector
     * @param StorageInterface $cache
     * @param ExampleFinder $exampleFinder
     * @param PartialsCollection $partials
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        ProjectDescriptorBuilder $builder,
        DocParser $parser,
        FileCollector $fileCollector,
        StorageInterface $cache,
        ExampleFinder $exampleFinder,
        PartialsCollection $partials,

        EventDispatcherInterface $eventDispatcher
    ) {
        $this->builder = $builder;
        $this->parser = $parser;
        $this->cache = $cache;
        $this->exampleFinder = $exampleFinder;
        $this->partials = $partials;
        $this->fileCollector = $fileCollector;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return ProjectDescriptorBuilder
     */
    private function getBuilder(): ProjectDescriptorBuilder
    {
        return $this->builder;
    }

    /**
     * @return DocParser
     */
    private function getParser(): DocParser
    {
        return $this->parser;
    }

    /**
     * Returns the Cache.
     *
     * @return StorageInterface
     * @throws \InvalidArgumentException
     */
    private function getCache(): StorageInterface
    {
        return $this->cache;
    }

    /**
     * Executes the business logic involved with this command.
     *
     * @param array $configuration
     * @return integer
     * @throws \Exception if the target location is not a folder.
     */
    public function __invoke(array $configuration)
    {
        $target = $configuration['phpdocumentor']['paths']['cache'];

        //Grep only the first version for now. Multi version support will be added later
        $version = current($configuration['phpdocumentor']['versions']);

        //We are currently in the parser stage so grep the api config.
        $apiConfig = $version['api'];

        //Process cache setup
        $fileSystem = new Filesystem();
        if (!$fileSystem->isAbsolutePath($target)) {
            $target = getcwd() . DIRECTORY_SEPARATOR . $target;
        }
        if (!file_exists($target)) {
            if (!mkdir($target) && !is_dir($target)) {
                throw new \RuntimeException('PPCPP:EXC-BADTARGET');
            }
        }

        $this->getCache()->getOptions()->setCacheDir($target);

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

        $visibility = $this->getVisibility($apiConfig);
        $projectDescriptor->getSettings()->setVisibility($visibility);

        $mapper = new ProjectDescriptorMapper($this->getCache());

        if ($configuration['phpdocumentor']['use-cache']) {
            //TODO: Re-enable garbage collection here.
            //$mapper->garbageCollect($files);
            $mapper->populate($projectDescriptor);
        }

        //TODO: Should determine root based on filesystems. Could be an issue for multiple.
        // Need some config update here.
        $this->exampleFinder->setSourceDirectory(getcwd());
        $this->exampleFinder->setExampleDirectories(['.']);

        $this->log('PPCPP:LOG-COLLECTING');
        $files = $this->getFileCollection($apiConfig);
        $this->log('PPCPP:LOG-OK');
        $this->log('PPCPP:LOG-INITIALIZING');


        $this->log('PPCPP:LOG-OK');
        $this->log('PPCPP:LOG-PARSING');

        $parser->parse($builder, $files);

        $this->log('PPCPP:LOG-STORECACHE', LogLevel::INFO, ['cacheDir' => $this->getCache()->getOptions()->getCacheDir()]);
        $projectDescriptor->getSettings()->clearModifiedFlag();
        $mapper->save($projectDescriptor);
        $this->log('PPCPP:LOG-OK');

        return 0;
    }

    /**
     * Returns the collection of files based on the input and configuration.
     *
     * @param array $apiConfig
     * @return array
     */
    private function getFileCollection(array $apiConfig): array
    {
        $ignorePaths = array_map(
            function ($value) {
                if (substr($value, -1) === '*') {
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
                'hidden' => $apiConfig['ignore']['hidden']
            ],
            $apiConfig['extensions']
        );
    }

    /**
     * @param $apiConfig
     * @return int|null
     */
    private function getVisibility($apiConfig)
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
     * @param string   $message  The message to log.
     * @param string   $priority The logging priority as declared in the LogLevel PSR-3 class.
     * @param string[] $parameters
     *
     * @return void
     */
    private function log($message, $priority = LogLevel::INFO, $parameters = array())
    {
        $this->eventDispatcher->dispatch(
            'system.log',
            LogEvent::createInstance($this)
                ->setContext($parameters)
                ->setMessage($message)
                ->setPriority($priority)
        );
    }
}
