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

namespace phpDocumentor\Parser;

use phpDocumentor\Descriptor\Analyzer;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Fileset\Collection;
use phpDocumentor\Parser\Event\PreFileEvent;
use phpDocumentor\Parser\Exception\FilesNotFoundException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class Parser
{
    const EVENT_FILES_COLLECTED       = 'parser.files.collected';
    const EVENT_BACKEND_BOOTED        = 'parser.backend.booted';
    const EVENT_BOOTED                = 'parser.booted';
    const EVENT_PARSE_FILE_BEFORE     = 'parser.file.pre';
    const EVENT_PARSE_FILE_AFTER      = 'parser.file.post';
    const EVENT_PARSE_FILE_NO_BACKEND = 'parser.file.noBackend';
    const EVENT_COMPLETED             = 'parser.completed';

    /** @var Fileset|null */
    private $fileset;

    /** @var Backend[] */
    private $backend;

    /** @var EventDispatcherInterface|null */
    private $dispatcher;

    /** @var Analyzer */
    private $analyzer;

    /** @var \phpDocumentor\Fileset\Collection */
    private $files;

    public function __construct(Analyzer $analyzer, Fileset $fileset = null)
    {
        $this->analyzer = $analyzer;
        $this->fileset = $fileset;
    }

    /**
     * Registers the mediator that will inform any attached listeners of events occurring in this parser.
     *
     * @param EventDispatcherInterface $dispatcher
     *
     * @return $this
     */
    public function registerEventDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;

        return $this;
    }

    /**
     * Registers a single parser backend to process a specific type of file with.
     *
     * The backend for a parser is able to handle a specific file type, interpret its contents and register the analyzed
     * output in a central location. phpDocumentor's backends write the analyzed output to the Project Descriptor using
     * the Analyzer in the Reflection component.
     *
     * Custom backends may be able to write to their own collectors and analyzers. This parser was explicitly designed
     * to allow that.
     *
     * @see \phpDocumentor\Descriptor\Analyzer
     * @see \phpDocumentor\Descriptor\ProjectDescriptor
     *
     * @param Backend $backend
     *
     * @return $this
     */
    public function registerBackend(Backend $backend)
    {
        $this->backend[] = $backend;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Initializes this parser and its backends using the provided configuration.
     *
     * @param Configuration $configuration
     *
     * @return $this
     */
    public function boot(Configuration $configuration)
    {
        $projectDescriptor = $this->createProject($configuration);
        $this->files = $this->scanForFiles($configuration);
        $this->dispatch(self::EVENT_FILES_COLLECTED, new GenericEvent($this->files));

        foreach ($this->backend as $backend) {
            $backend->boot($configuration);
            $this->dispatch(self::EVENT_BACKEND_BOOTED, new GenericEvent($backend));
        }

        $this->dispatch(
            self::EVENT_BOOTED,
            new GenericEvent($projectDescriptor, array('files' => $this->files, 'configuration' => $configuration))
        );

        return $this;
    }

    /**
     * @param \SplFileInfo[] $files
     */
    public function parse()
    {
        foreach ($this->files as $file) {
            $this->parseFile($file);
        }

        $projectDescriptor = $this->analyzer->getProjectDescriptor();
        $projectDescriptor->getSettings()->clearModifiedFlag();

        if ($this->dispatcher) {
            $this->dispatcher->dispatch(self::EVENT_COMPLETED, new GenericEvent($projectDescriptor));
        }

        return $projectDescriptor;
    }

    /**
     * @return Analyzer
     */
    private function getAnalyzer()
    {
        return $this->analyzer;
    }

    /**
     * @param \SplFileInfo $file
     */
    private function parseFile(\SplFileInfo $file)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(
                self::EVENT_PARSE_FILE_BEFORE,
                (new PreFileEvent($this))->setFile($file->getPath())
            );
        }

        foreach ($this->backend as $backend) {
            if ($backend->matches($file)) {
                $backend->parse($file->openFile());

                if ($this->dispatcher) {
                    $this->dispatcher->dispatch(self::EVENT_PARSE_FILE_AFTER, new GenericEvent($file));
                }
                return;
            }
        }

        if ($this->dispatcher) {
            $this->dispatcher->dispatch(self::EVENT_PARSE_FILE_NO_BACKEND, new GenericEvent($file));
        }
    }

    /**
     * @param Configuration $configuration
     *
     * @return ProjectDescriptor
     */
    private function createProject(Configuration $configuration)
    {
        $this->getAnalyzer()->createProjectDescriptor();
        $projectDescriptor = $this->getAnalyzer()->getProjectDescriptor();

        $map = array(
            'public' => ProjectDescriptor\Settings::VISIBILITY_PUBLIC,
            'protected' => ProjectDescriptor\Settings::VISIBILITY_PROTECTED,
            'private' => ProjectDescriptor\Settings::VISIBILITY_PRIVATE,
            'default' => ProjectDescriptor\Settings::VISIBILITY_DEFAULT,
            'internal' => ProjectDescriptor\Settings::VISIBILITY_INTERNAL
        );

        $visibilities = explode(',', $configuration->getVisibility());
        $visibility = null;
        foreach ($visibilities as $item) {
            if (!$item) {
                continue;
            }

            $visibility |= $map[$item];
        }
        $projectDescriptor->getSettings()->setVisibility($visibility);

        return $projectDescriptor;
    }

    /**
     * @param Configuration $configuration
     * @return Collection
     * @throws \Exception
     */
    private function scanForFiles(Configuration $configuration)
    {
        $fileset = $this->fileset ?: new Fileset();
        try {
            $files = $fileset->populate(new Collection(), $configuration);
            $configuration->setProjectRoot($files->getProjectRoot());
        } catch (FilesNotFoundException $e) {
            throw new \Exception('PPCPP:EXC-NOFILES');
        }

        return $files;
    }

    /**
     * @param $eventName
     * @param $event
     */
    private function dispatch($eventName, $event)
    {
        if ($this->dispatcher instanceof EventDispatcherInterface) {
            $this->dispatcher->dispatch($eventName, $event);
        }
    }
}
