<?php

namespace phpDocumentor\Parser\Backend;

use phpDocumentor\Descriptor\Analyzer;
use phpDocumentor\Descriptor\Builder\PhpParser\FileAssembler;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Parser\Backend;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class Php implements Backend
{
    const EVENT_FILE_IS_CACHED = 'parser.file.isCached';
    const EVENT_ANALYZED_FILE  = 'parser.file.analyzed';

    /** @var string[] */
    private $extensions;

    /** @var Analyzer */
    private $analyzer;

    /** @var EventDispatcherInterface|null */
    private $dispatcher;

    public function __construct(Analyzer $analyzer)
    {
        $this->analyzer = $analyzer;
    }

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function boot(\phpDocumentor\Configuration $configuration)
    {
        $this->extensions = $configuration->getParser()->getExtensions();

        $this->setAssemblerConfiguration($configuration);
    }

    public function matches(\SplFileInfo $file)
    {
        return in_array($file->getExtension(), $this->extensions);
    }

    public function parse(\SplFileObject $file)
    {
        $descriptor = $this->findCachedDescriptor($file);
        $hash = md5(file_get_contents($file->getPathname()));
        if ($descriptor instanceof FileDescriptor && $descriptor->getHash() == $hash) {
            if ($this->dispatcher) {
                $this->dispatcher->dispatch(self::EVENT_FILE_IS_CACHED, new GenericEvent($descriptor));
            }

            return $descriptor;
        }

        $descriptor = $this->analyzer->analyze($file);
        if ($this->dispatcher && $descriptor instanceof FileDescriptor) {
            $this->dispatcher->dispatch(self::EVENT_ANALYZED_FILE, new GenericEvent($descriptor));
        }

        return $descriptor;
    }

    /**
     * @param \SplFileObject $file
     */
    private function findCachedDescriptor(\SplFileObject $file)
    {
        $cachedFiles = $this->analyzer->getProjectDescriptor()->getFiles();

        return $cachedFiles->get($file->getPathname());
    }

    /**
     * @param \phpDocumentor\Configuration $configuration
     */
    private function setAssemblerConfiguration(\phpDocumentor\Configuration $configuration)
    {
        $fileAssembler = $this->analyzer->getAssemblerFactory(new \SplFileObject(__FILE__));
        if ($fileAssembler instanceof FileAssembler) {
            $fileAssembler->setDefaultPackageName($configuration->getParser()->getDefaultPackageName());
            $fileAssembler->setEncoding($configuration->getParser()->getEncoding());
            $fileAssembler->setMarkerTerms($configuration->getParser()->getMarkers());
        }
    }
}
