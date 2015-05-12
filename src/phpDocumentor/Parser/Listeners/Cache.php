<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.4
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Parser\Listeners;

use Desarrolla2\Cache\Adapter\File;
use Desarrolla2\Cache\CacheInterface;
use phpDocumentor\Descriptor\Cache\ProjectDescriptorMapper;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\Interfaces\ProjectInterface;
use phpDocumentor\Fileset\Collection;
use phpDocumentor\Parser\Parser;
use phpDocumentor\Translator\Translator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Listener that will add caching capabilities to the parser.
 */
class Cache
{
    const EVENT_CACHE_LOADED = 'parser.cache.loaded';

    /** @var CacheInterface */
    private $cacheModel;

    /** @var Translator */
    private $translator;

    /** @var ProjectDescriptorMapper */
    private $mapper;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    /**
     * Initializes this listener with the caching service and a translator.
     *
     * @param CacheInterface $cacheModel
     * @param Translator $translator
     */
    public function __construct(CacheInterface $cacheModel, Translator $translator)
    {
        $this->cacheModel = $cacheModel;
        $this->translator = $translator;
        $this->mapper     = new ProjectDescriptorMapper($cacheModel);
    }

    /**
     * Registers the events to which this listener is listening to the given event dispatcher.
     *
     * @param EventDispatcherInterface $dispatcher
     *
     * @return void
     */
    public function register(EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addListener(Parser::EVENT_BOOTED, array($this, 'onBooted'));
        $dispatcher->addListener(Parser::EVENT_COMPLETED, array($this, 'onCompleted'));

        $this->dispatcher = $dispatcher;
    }

    /**
     * When the parser is finished booting we load the cache into the given ProjectDescriptor (the subject of the
     * event).
     *
     * When the cache is finished loading this event will fire another event to indicate that. Should the configuration
     * indicate that no caching should be done we clear the cache before loading it.
     *
     * @param GenericEvent $event
     *
     * @return void
     */
    public function onBooted(GenericEvent $event)
    {
        $files         = $event->getArgument('files');
        $configuration = $event->getArgument('configuration');

        $this->setLocation($configuration->getTarget());
        if ($configuration->shouldRebuildCache()) {
            $this->clear();
        }
        $this->load($files, $event->getSubject());

        if ($this->dispatcher instanceof EventDispatcherInterface) {
            $this->dispatcher->dispatch(self::EVENT_CACHE_LOADED, new GenericEvent($event->getSubject()));
        }
    }

    /**
     * When the parser is finished we store the cache.
     *
     * @param GenericEvent $event
     *
     * @return void
     */
    public function onCompleted(GenericEvent $event)
    {
        $this->save($event->getSubject());
    }

    /**
     * Populates the ProjectDescriptor with the cache and removes any file from the project descriptor that we do not
     * intend to parse.
     *
     * By removing all files that we do not want to parse we automatically clean up the cached information by removing
     * deleted entries and making sure no unwanted elements are loaded.
     *
     * @param Collection        $files
     * @param ProjectInterface $projectDescriptor
     *
     * @return void
     */
    private function load(Collection $files, ProjectInterface $projectDescriptor)
    {
        $this->mapper->populate($projectDescriptor);

        // Remove any cached file that is not in the list of scanned files to clean up for deleted
        // or unwanted files.
        $cachedFilenames = array_keys($projectDescriptor->getFiles()->getAll());
        $scannedFilenames = $files->getFilenames();

        // find each cached filename not in the list of scanned filenames
        $removeFiles = array_diff($cachedFilenames, $scannedFilenames);
        foreach ($removeFiles as $filename) {
            $projectDescriptor->getFiles()->offsetUnset($filename);
        }
    }

    /**
     * Clear all but the settings from cache.
     *
     * @return void
     */
    private function clear()
    {
        $this->getCache()->delete(ProjectDescriptorMapper::KEY_FILES);
    }

    /**
     * Stores the project descriptor in the cache.
     *
     * @param ProjectInterface $projectDescriptor
     *
     * @return void
     */
    private function save(ProjectInterface $projectDescriptor)
    {
        $this->mapper->save($projectDescriptor);
    }

    /**
     * Sets the location for the cache based on the given target.
     *
     * @param string $target
     *
     * @throws \Exception if no valid target location was found.
     *
     * @return void
     */
    private function setLocation($target)
    {
        if (strpos($target, '/tmp/') === 0) {
            $target = str_replace('/tmp/', sys_get_temp_dir() . DIRECTORY_SEPARATOR, $target);
        }
        $fileSystem = new Filesystem();
        if (!$fileSystem->isAbsolutePath($target)) {
            $target = getcwd() . DIRECTORY_SEPARATOR . $target;
        }
        if (!file_exists($target)) {
            mkdir($target, 0777, true);
        }
        if (!is_dir($target)) {
            throw new \Exception($this->translator->translate('PPCPP:EXC-BADTARGET'));
        }
        $this->getCache()->setAdapter(new File($target));
    }

    /**
     * Returns the cache manager that will persist the project descriptor.
     *
     * @return CacheInterface
     */
    private function getCache()
    {
        return $this->cacheModel;
    }
}
