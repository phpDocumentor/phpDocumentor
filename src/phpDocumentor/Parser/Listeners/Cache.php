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

namespace phpDocumentor\Parser\Listeners;

use Desarrolla2\Cache\Adapter\File;
use Desarrolla2\Cache\CacheInterface;
use phpDocumentor\Descriptor\Cache\ProjectDescriptorMapper;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Fileset\Collection;
use phpDocumentor\Parser\Parser;
use phpDocumentor\Translator\Translator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Filesystem\Filesystem;

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

    public function __construct(CacheInterface $cacheModel, Translator $translator)
    {
        $this->cacheModel = $cacheModel;
        $this->translator = $translator;
        $this->mapper     = new ProjectDescriptorMapper($cacheModel);
    }

    public function register(EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addListener(Parser::EVENT_BOOTED, array($this, 'onBooted'));
        $dispatcher->addListener(Parser::EVENT_COMPLETED, array($this, 'onCompleted'));

        $this->dispatcher = $dispatcher;
    }

    public function onBooted(GenericEvent $event)
    {
        $files         = $event->getArgument('files');
        $configuration = $event->getArgument('configuration');

        $this->setLocation($configuration->getTarget());
        if ($configuration->shouldRebuildCache()) {
            $this->clear();
        }
        $this->load($files, $event->getSubject());

        if ($this->dispatcher) {
            $this->dispatcher->dispatch(self::EVENT_CACHE_LOADED, new GenericEvent($event->getSubject()));
        }
    }

    public function onCompleted(GenericEvent $event)
    {
        $this->save($event->getSubject());
    }

    /**
     * @param Collection $files
     * @param ProjectDescriptor $projectDescriptor
     * @return ProjectDescriptorMapper
     */
    private function load(Collection $files, ProjectDescriptor $projectDescriptor)
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

    private function save(ProjectDescriptor $projectDescriptor)
    {
        $this->mapper->save($projectDescriptor);
    }

    /**
     * @param $target
     * @throws \Exception
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
            throw new \Exception($this->translator->_('PPCPP:EXC-BADTARGET'));
        }
        $this->getCache()->setAdapter(new File($target));
    }

    /**
     * @return CacheInterface
     */
    private function getCache()
    {
        return $this->cacheModel;
    }
}