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

namespace phpDocumentor\Parser\Backend;

use phpDocumentor\Descriptor\Analyzer;
use phpDocumentor\Descriptor\Builder\PhpParser\FileAssembler;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Parser\Backend;
use phpDocumentor\Parser\Configuration;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Backend used to analyze PHP files and store their structure in the ProjectDescriptor.
 */
final class Php implements Backend
{
    const EVENT_FILE_IS_CACHED = 'parser.file.isCached';
    const EVENT_ANALYZED_FILE  = 'parser.file.analyzed';

    /**
     * An array of supported file extensions for this backend.
     *
     * @var string[]
     */
    private $extensions;

    /**
     * The analyzer that is used both to analyze the file's contents and with which to access the project descriptor to
     * store the results on.
     *
     * @var Analyzer
     */
    private $analyzer;

    /**
     * An optional event mediator.
     *
     * When provided this backend is capable of sending events with which another part of the application knows when
     * a file is processed.
     *
     * @var EventDispatcherInterface|null
     */
    private $dispatcher;

    /**
     * Registers the Analyzer with this backend.
     *
     * @param Analyzer $analyzer
     */
    public function __construct(Analyzer $analyzer)
    {
        $this->analyzer = $analyzer;
    }

    /**
     * Registers the Event Dispatcher with this backend.
     *
     * @param EventDispatcherInterface $dispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @inheritDoc
     */
    public function boot(Configuration $configuration)
    {
        $this->extensions = array('php', 'phtml', 'php3');

        $this->setAssemblerConfiguration($configuration);
    }

    /**
     * @inheritDoc
     */
    public function matches(\SplFileInfo $file)
    {
        return in_array($file->getExtension(), $this->extensions);
    }

    /**
     * Analyzes the file's contents as a PHP file and stores the result in the Project Descriptor in the Analyzer that
     * is associated with this object.
     *
     * When an analyzed file with the exact same path is present in the Project Descriptor and its contents do not
     * differ from the provided file (calculated by hash) than the analysis is skipped and the existing file in the
     * Project Descriptor is considered valid.
     *
     * This method also dispatches the {@see EVENT_FILE_IS_CACHED} event if the file is cached and not re-analyzed.
     *
     * @param \SplFileObject $file
     *
     * @return void
     */
    public function parse(\SplFileObject $file)
    {
        if ($this->isCached($file)) {
            $descriptor = $this->fetchCachedDescriptor($file);
            $this->dispatchIsCachedEvent($descriptor);
            return;
        }

        $this->analyzeFile($file);
    }

    /**
     * Pre-sets the PHP-Parser assembler for File Descriptors with configuration values.
     *
     * This method will attempt to find the PHP-Parser File Assembler used to construct File Descriptor objects with
     * and configure it with the Default Package Name, Encoding and Markers so that it can properly extract this
     * information from a file.
     *
     * @param Configuration $configuration
     *
     * @return void
     */
    private function setAssemblerConfiguration(Configuration $configuration)
    {
        $fileAssembler = $this->analyzer->getAssembler(new \SplFileObject(__FILE__));
        if ($fileAssembler instanceof FileAssembler) {
            $fileAssembler->setDefaultPackageName($configuration->getDefaultPackageName());
            $fileAssembler->setEncoding($configuration->getEncoding());
            $fileAssembler->setMarkerTerms($configuration->getMarkers());
            $fileAssembler->setProjectRoot($configuration->getProjectRoot());
        }
    }

    /**
     * Determines whether the given file is present in the Project Descriptor with the given Analyzer and its contents
     * have not changed.
     *
     * @param \SplFileObject $file
     *
     * @return bool
     */
    private function isCached(\SplFileObject $file)
    {
        $descriptor = $this->fetchCachedDescriptor($file);

        return $descriptor instanceof FileDescriptor && $descriptor->getHash() == $this->createHash($file);
    }

    /**
     * Calculates the hash used to determine whether the file's contents differ from the cached version.
     *
     * @param \SplFileObject $file
     *
     * @return string
     */
    private function createHash(\SplFileObject $file)
    {
        return md5(file_get_contents($file->getPathname()));
    }

    /**
     * Fetches the file descriptor from the project descriptor for the given file's path or returns null if no such file
     * exists in the project descriptor.
     *
     * @param \SplFileObject $file
     *
     * @return FileDescriptor|null
     */
    private function fetchCachedDescriptor(\SplFileObject $file)
    {
        return $this->analyzer->getProjectDescriptor()->getFiles()->get($file->getPathname());
    }

    /**
     * Analyze the given file and store the results in the Project Descriptor.
     *
     * Please note that the persisting of a FileDescriptor is part of the `analyze()` method of the Analyzer for PHP
     * files. That means that the code in this method does not show that the File Descriptor is persisted but it is.
     *
     * This method also dispatches the {@see EVENT_ANALYZED_FILE} event.
     *
     * @param \SplFileObject $file
     *
     * @return void
     */
    private function analyzeFile(\SplFileObject $file)
    {
        $descriptor = $this->analyzer->analyze($file);
        $this->dispatchIsAnalyzedEvent($descriptor);
    }

    /**
     * Dispatches an event indicating that the given FileDescriptor was not renewed but a cached version is usd instead.
     *
     * @param FileDescriptor $descriptor
     *
     * @return void
     */
    private function dispatchIsCachedEvent(FileDescriptor $descriptor)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(self::EVENT_FILE_IS_CACHED, new GenericEvent($descriptor));
        }
    }

    /**
     * Dispatches and event indicating that the given FileDescriptor was analyzed and a new version was stored in the
     * ProjectDescriptor.
     *
     * @param FileDescriptor $descriptor
     *
     * @return void
     */
    private function dispatchIsAnalyzedEvent(FileDescriptor $descriptor)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(self::EVENT_ANALYZED_FILE, new GenericEvent($descriptor));
        }
    }
}
