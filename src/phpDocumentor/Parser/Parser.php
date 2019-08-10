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

namespace phpDocumentor\Parser;

use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Event\Dispatcher;
use phpDocumentor\Parser\Event\PreParsingEvent;
use phpDocumentor\Parser\Exception\FilesNotFoundException;
use phpDocumentor\Reflection\ProjectFactory;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Class responsible for parsing the given file or files to the intermediate
 * structure file.
 *
 * This class can be used to parse one or more files to the intermediate file
 * format for further processing.
 */
class Parser
{
    /** @var string the name of the default package */
    protected $defaultPackageName = 'Default';

    /** @var bool whether we force a full re-parse */
    protected $force = false;

    /** @var bool whether to execute a PHPLint on every file */
    protected $validate = false;

    /** @var string[] which markers (i.e. TODO or FIXME) to collect */
    protected $markers = ['TODO', 'FIXME'];

    /** @var string[] which tags to ignore */
    protected $ignoredTags = [];

    /** @var string target location's root path */
    protected $path = '';

    /** @var LoggerInterface $logger */
    protected $logger;

    /** @var string The encoding in which the files are encoded */
    protected $encoding = 'utf-8';

    /** @var Stopwatch $stopwatch The profiling component that measures time and memory usage over time */
    protected $stopwatch = null;

    /**
     * @var ProjectFactory
     */
    private $projectFactory;

    /**
     * Initializes the parser.
     *
     * This constructor checks the user's PHP ini settings to detect which encoding is used by default. This encoding
     * is used as a default value for phpDocumentor to convert the source files that it receives.
     *
     * If no encoding is specified than 'utf-8' is assumed by default.
     *
     * @codeCoverageIgnore the ini_get call cannot be tested as setting it using ini_set has no effect.
     */
    public function __construct(ProjectFactory $projectFactory, Stopwatch $stopwatch, LoggerInterface $logger)
    {
        $defaultEncoding = ini_get('zend.script_encoding');
        if ($defaultEncoding) {
            $this->encoding = $defaultEncoding;
        }

        $this->projectFactory = $projectFactory;
        $this->stopwatch = $stopwatch;
        $this->logger = $logger;
    }

    /**
     * Sets whether to force a full parse run of all files.
     *
     * @param bool $forced Forces a full parse.
     *
     * @api
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
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Returns the absolute base path for all files.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Sets the name of the default package.
     *
     * @param string $defaultPackageName Name used to categorize elements
     *  without an @package tag.
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
     * @return \phpDocumentor\Reflection\Php\Project
     *
     * @throws FilesNotFoundException if no files were found.
     * @api
     */
    public function parse(array $files)
    {
        $this->startTimingTheParsePhase();

        $event = PreParsingEvent::createInstance($this);
        assert($event instanceof PreParsingEvent);
        Dispatcher::getInstance()
            ->dispatch(
                'parser.pre',
                $event->setFileCount(count($files))
            );

        /** @var \phpDocumentor\Reflection\Php\Project $project */
        $project = $this->projectFactory->create(ProjectDescriptorBuilder::DEFAULT_PROJECT_NAME, $files);
        $this->logAfterParsingAllFiles();

        return $project;
    }

    /**
     * Writes the complete parsing cycle to log.
     */
    private function logAfterParsingAllFiles()
    {
        if (!$this->stopwatch) {
            return;
        }

        $event = $this->stopwatch->stop('parser.parse');

        $this->log('Elapsed time to parse all files: ' . round($event->getDuration() / 1000, 2) . 's');
        $this->log('Peak memory usage: ' . round($event->getMemory() / 1024 / 1024, 2) . 'M');
    }

    /**
     * Dispatches a logging request.
     *
     * @param string   $message  The message to log.
     * @param string   $priority The logging priority as declared in the LogLevel PSR-3 class.
     * @param string[] $parameters
     */
    private function log($message, $priority = LogLevel::INFO, $parameters = [])
    {
        $this->logger->log($priority, $message, $parameters);
    }

    private function startTimingTheParsePhase()
    {
        if ($this->stopwatch) {
            $this->stopwatch->start('parser.parse');
        }
    }
}
