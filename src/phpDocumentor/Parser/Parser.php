<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Parser;

use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Event\DebugEvent;
use phpDocumentor\Event\Dispatcher;
use phpDocumentor\Event\LogEvent;
use phpDocumentor\Fileset\Collection;
use phpDocumentor\Parser\Event\PreFileEvent;
use phpDocumentor\Parser\Exception\FilesNotFoundException;
use phpDocumentor\Reflection\FileReflector;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Class responsible for parsing the given file or files to the intermediate
 * structure file.
 *
 * This class can be used to parse one or more files to the intermediate file
 * format for further processing.
 *
 * Example of use:
 *
 *     $files = new \phpDocumentor\File\Collection();
 *     $ files->addDirectories(getcwd());
 *     $parser = new \phpDocumentor\Parser\Parser();
 *     $parser->setPath($files->getProjectRoot());
 *     echo $parser->parseFiles($files);
 */
class Parser implements LoggerAwareInterface
{
    /** @var string the name of the default package */
    protected $defaultPackageName = 'Default';

    /** @var bool whether we force a full re-parse */
    protected $force = false;

    /** @var bool whether to execute a PHPLint on every file */
    protected $validate = false;

    /** @var string[] which markers (i.e. TODO or FIXME) to collect */
    protected $markers = array('TODO', 'FIXME');

    /** @var string[] which tags to ignore */
    protected $ignoredTags = array();

    /** @var string target location's root path */
    protected $path = null;

    /** @var LoggerInterface $logger */
    protected $logger;

    /**
     * Array of visibility modifiers that should be adhered to when generating
     * the documentation
     *
     * @var array
     */
    protected $visibility = array('public', 'protected', 'private');

    /** @var string The encoding in which the files are encoded */
    protected $encoding = 'utf-8';

    /** @var Stopwatch $stopwatch The profiling component that measures time and memory usage over time */
    protected $stopwatch = null;

    /**
     * Initializes the parser.
     *
     * This constructor checks the user's PHP ini settings to detect which encoding is used by default. This encoding
     * is used as a default value for phpDocumentor to convert the source files that it receives.
     *
     * If no encoding is specified than 'utf-8' is assumed by default.
     */
    public function __construct()
    {
        $defaultEncoding = ini_get('zend.script_encoding');
        if ($defaultEncoding) {
            $this->encoding = $defaultEncoding;
        }
    }

    /**
     * Registers the component that profiles the execution of the parser.
     *
     * @param Stopwatch $stopwatch
     *
     * @return void
     */
    public function setStopwatch($stopwatch)
    {
        $this->stopwatch = $stopwatch;
    }

    /**
     * Sets whether to force a full parse run of all files.
     *
     * @param bool $forced Forces a full parse.
     *
     * @api
     *
     * @return void
     */
    public function setForced($forced)
    {
        $this->force = $forced;
    }

    /**
     * Returns whether a full rebuild is required.
     *
     * @api
     *
     * @return bool
     */
    public function isForced()
    {
        return $this->force;
    }

    /**
     * Sets whether to run PHPLint on every file.
     *
     * PHPLint has a huge performance impact on the execution of phpDocumentor and
     * is thus disabled by default.
     *
     * @param bool $validate when true this file will be checked.
     *
     * @api
     *
     * @return void
     */
    public function setValidate($validate)
    {
        $this->validate = $validate;
    }

    /**
     * Returns whether we want to run PHPLint on every file.
     *
     * @api
     *
     * @return bool
     */
    public function doValidation()
    {
        return $this->validate;
    }

    /**
     * Sets a list of markers to gather (i.e. TODO, FIXME).
     *
     * @param string[] $markers A list or markers to gather.
     *
     * @api
     *
     * @return void
     */
    public function setMarkers(array $markers)
    {
        $this->markers = $markers;
    }

    /**
     * Returns the list of markers.
     *
     * @api
     *
     * @return string[]
     */
    public function getMarkers()
    {
        return $this->markers;
    }

    /**
     * Sets a list of tags to ignore.
     *
     * @param string[] $ignoredTags A list of tags to ignore.
     *
     * @api
     *
     * @return void
     */
    public function setIgnoredTags(array $ignoredTags)
    {
        $this->ignoredTags = $ignoredTags;
    }

    /**
     * Returns the list of ignored tags.
     *
     * @api
     *
     * @return string[]
     */
    public function getIgnoredTags()
    {
        return $this->ignoredTags;
    }

    /**
     * Sets the base path of the files that will be parsed.
     *
     * @param string $path Must be an absolute path.
     *
     * @api
     *
     * @return void
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Set the visibility of the methods/properties that should be documented
     *
     * @param string $visibility Comma seperated string of visibility modifiers
     *
     * @api
     *
     * @return void
     */
    public function setVisibility($visibility)
    {
        if ('' != $visibility) {
            $this->visibility = explode(',', $visibility);
        }
    }

    /**
     * Returns which elements' are allowed to be returned by visibility.
     *
     * @return string[]
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * Returns the filename, relative to the root of the project directory.
     *
     * @param string $filename The filename to make relative.
     *
     * @throws \InvalidArgumentException if file is not in the project root.
     *
     * @return string
     */
    public function getRelativeFilename($filename)
    {
        // strip path from filename
        $result = ltrim(substr($filename, strlen($this->path)), DIRECTORY_SEPARATOR);
        if ($result === '') {
            throw new \InvalidArgumentException(
                'File is not present in the given project path: ' . $filename
            );
        }

        return $result;
    }

    /**
     * Sets the name of the default package.
     *
     * @param string $defaultPackageName Name used to categorize elements
     *  without an @package tag.
     *
     * @return void
     */
    public function setDefaultPackageName($defaultPackageName)
    {
        $this->defaultPackageName = $defaultPackageName;
    }

    /**
     * Returns the name of the default package.
     *
     * @return string
     */
    public function getDefaultPackageName()
    {
        return $this->defaultPackageName;
    }

    /**
     * Sets the encoding of the files.
     *
     * With this option it is possible to tell the parser to use a specific encoding to interpret the provided files.
     * By default this is set to UTF-8, in which case no action is taken. Any other encoding will result in the output
     * being converted to UTF-8 using `iconv`.
     *
     * Please note that it is recommended to provide files in UTF-8 format; this will ensure a faster performance since
     * no transformation is required.
     *
     * @param string $encoding
     *
     * @return void
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
    }

    /**
     * Returns the currently active encoding.
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * Iterates through the given files feeds them to the builder.
     *
     * @param ProjectDescriptorBuilder $builder
     * @param Collection               $files   A files container to parse.
     *
     * @api
     *
     * @throws Exception if no files were found.
     *
     * @return bool|string
     */
    public function parse(ProjectDescriptorBuilder $builder, Collection $files)
    {
        if ($this->stopwatch) {
            $this->stopwatch->start('parser.parse');
        }

        $paths = $this->getFilenames($files);

        $this->log('  Project root is:  ' . $files->getProjectRoot());
        $this->log('  Ignore paths are: ' . implode(', ', $files->getIgnorePatterns()->getArrayCopy()));

        if ($builder->getProjectDescriptor()->getSettings()->isModified()) {
            $this->setForced(true);
            $this->log('One of the project\'s settings have changed, forcing a complete rebuild');
        }

        $memory = 0;
        foreach ($paths as $filename) {
            $this->parseFileIntoDescriptor($builder, $filename);

            $lap       = $this->stopwatch->lap('parser.parse');
            $oldMemory = $memory;
            $periods   = $lap->getPeriods();
            $memory    = end($periods)->getMemory();

            $this->log(
                '>> Memory after processing of file: ' . number_format($memory / 1024 / 1024, 2)
                . ' megabytes (' . (($memory - $oldMemory >= 0)
                    ? '+'
                    : '-') . number_format(($memory - $oldMemory) / 1024)
                . ' kilobytes)',
                LogLevel::DEBUG
            );
        }

        if ($this->stopwatch) {
            $event = $this->stopwatch->stop('parser.parse');

            $this->log('Elapsed time to parse all files: ' . round($event->getDuration(), 2) . 's');
            $this->log('Peak memory usage: '. round($event->getMemory() / 1024 / 1024, 2) . 'M');
        }

        return $builder->getProjectDescriptor();
    }

    /**
     * Extract all filenames from the given collection and output the amount of files.
     *
     * @param Collection $files
     *
     * @throws FilesNotFoundException if no files were found.
     *
     * @return string[]
     */
    protected function getFilenames(Collection $files)
    {
        $paths = $files->getFilenames();
        if (count($paths) < 1) {
            throw new FilesNotFoundException();
        }
        $this->log('Starting to process ' . count($paths) . ' files');

        return $paths;
    }

    /**
     * Parses a file and creates a Descriptor for it in the project.
     *
     * @param ProjectDescriptorBuilder $builder
     * @param string                   $filename
     *
     * @return void
     */
    protected function parseFileIntoDescriptor(ProjectDescriptorBuilder $builder, $filename)
    {
        if (class_exists('phpDocumentor\Event\Dispatcher')) {
            Dispatcher::getInstance()->dispatch(
                'parser.file.pre',
                PreFileEvent::createInstance($this)->setFile($filename)
            );
        }
        $this->log('Starting to parse file: ' . $filename);

        try {
            $file = $this->createFileReflector($builder, $filename);
            if (!$file) {
                $this->log('>> Skipped file ' . $filename . ' as no modifications were detected');
                return;
            }

            $file->process();
            $builder->buildFileUsingSourceData($file);
            $this->logErrorsForDescriptor($builder->getProjectDescriptor()->getFiles()->get($file->getFilename()));
        } catch (Exception $e) {
            $this->log(
                '  Unable to parse file "' . $filename . '", an error was detected: ' . $e->getMessage(),
                LogLevel::ALERT
            );
        }
    }

    /**
     * Creates a new FileReflector for the given filename or null if the file contains no modifications.
     *
     * @param ProjectDescriptorBuilder $builder
     * @param string                   $filename
     *
     * @return FileReflector|null Returns a new FileReflector or null if no modifications were detected for the given
     *     filename.
     */
    protected function createFileReflector(ProjectDescriptorBuilder $builder, $filename)
    {
        $file = new FileReflector($filename, $this->doValidation(), $this->getEncoding());
        $file->setDefaultPackageName($this->getDefaultPackageName());
        $file->setMarkers($this->getMarkers());
        $file->setFilename($this->getRelativeFilename($filename));

        $cachedFiles = $builder->getProjectDescriptor()->getFiles();
        $hash        = $cachedFiles->get($file->getFilename())
            ? $cachedFiles->get($file->getFilename())->getHash()
            : null;

        return $hash === $file->getHash() && !$this->isForced()
            ? null
            : $file;
    }

    /**
     * Writes the errors found in the Descriptor to the log.
     *
     * @param FileDescriptor $fileDescriptor
     *
     * @return void
     */
    protected function logErrorsForDescriptor($fileDescriptor)
    {
        $errors = $fileDescriptor->getAllErrors();
        foreach ($errors as $error) {
            $this->log($error->getCode(), $error->getSeverity(), $error->getContext());
        }
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
    protected function log($message, $priority = LogLevel::INFO, $parameters = array())
    {
        Dispatcher::getInstance()->dispatch(
            'system.log',
            LogEvent::createInstance($this)
            ->setContext($parameters)
            ->setMessage($message)
            ->setPriority($priority)
        );
    }

    /**
     * Dispatches a logging request to log a debug message.
     *
     * @param string $message The message to log.
     *
     * @return void
     */
    protected function debug($message)
    {
        Dispatcher::getInstance()->dispatch(
            'system.debug',
            DebugEvent::createInstance($this)->setMessage($message)
        );
    }

    /**
     * Sets a logger instance on the object
     *
     * @param LoggerInterface $logger
     *
     * @return null
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
