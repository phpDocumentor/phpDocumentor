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

namespace phpDocumentor\Parser;

use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Event\Dispatcher;
use phpDocumentor\Parser\Event\PreParsingEvent;
use phpDocumentor\Reflection\File;
use phpDocumentor\Reflection\Php\Project;
use phpDocumentor\Reflection\ProjectFactory;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Stopwatch\Stopwatch;
use Webmozart\Assert\Assert;

use function count;
use function ini_get;
use function round;

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
    private $defaultPackageName = 'Default';

    /** @var bool whether to execute a PHPLint on every file */
    private $validate = false;

    /** @var string[] which markers (i.e. TODO or FIXME) to collect */
    private $markers = ['TODO', 'FIXME'];

    /** @var string target location's root path */
    private $path = '';

    /** @var LoggerInterface $logger */
    private $logger;

    /** @var string The encoding in which the files are encoded */
    private $encoding = 'utf-8';

    /** @var Stopwatch $stopwatch The profiling component that measures time and memory usage over time */
    private $stopwatch;

    /** @var ProjectFactory */
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
     * Sets whether to run PHPLint on every file.
     *
     * PHPLint has a huge performance impact on the execution of phpDocumentor and
     * is thus disabled by default.
     *
     * @param bool $validate when true this file will be checked.
     */
    public function setValidate(bool $validate): void
    {
        $this->validate = $validate;
    }

    /**
     * Returns whether we want to run PHPLint on every file.
     */
    public function doValidation(): bool
    {
        return $this->validate;
    }

    /**
     * Sets a list of markers to gather (i.e. TODO, FIXME).
     *
     * @param string[] $markers A list or markers to gather.
     */
    public function setMarkers(array $markers): void
    {
        $this->markers = $markers;
    }

    /**
     * Returns the list of markers.
     *
     * @return string[]
     */
    public function getMarkers(): array
    {
        return $this->markers;
    }

    /**
     * Sets the base path of the files that will be parsed.
     *
     * @param string $path Must be an absolute path.
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * Returns the absolute base path for all files.
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Sets the name of the default package.
     *
     * @param string $defaultPackageName Name used to categorize elements
     *  without an @package tag.
     */
    public function setDefaultPackageName(string $defaultPackageName): void
    {
        $this->defaultPackageName = $defaultPackageName;
    }

    /**
     * Returns the name of the default package.
     */
    public function getDefaultPackageName(): string
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
     */
    public function setEncoding(string $encoding): void
    {
        $this->encoding = $encoding;
    }

    /**
     * Returns the currently active encoding.
     */
    public function getEncoding(): string
    {
        return $this->encoding;
    }

    /**
     * Iterates through the given files feeds them to the builder.
     *
     * @param File[] $files
     */
    public function parse(array $files): Project
    {
        $this->startTimingTheParsePhase();

        $event = PreParsingEvent::createInstance($this);
        Assert::isInstanceOf($event, PreParsingEvent::class);
        Dispatcher::getInstance()
            ->dispatch(
                $event->setFileCount(count($files)),
                'parser.pre'
            );

        /** @var Project $project */
        $project = $this->projectFactory->create(ProjectDescriptorBuilder::DEFAULT_PROJECT_NAME, $files);
        $this->logAfterParsingAllFiles();

        return $project;
    }

    /**
     * Writes the complete parsing cycle to log.
     */
    private function logAfterParsingAllFiles(): void
    {
        $event = $this->stopwatch->stop('parser.parse');

        $this->log('Elapsed time to parse all files: ' . round($event->getDuration() / 1000, 2) . 's');
        $this->log('Peak memory usage: ' . round($event->getMemory() / 1024 / 1024, 2) . 'M');
    }

    /**
     * Dispatches a logging request.
     *
     * @param string $message The message to log.
     * @param string $priority The logging priority as declared in the LogLevel PSR-3 class.
     * @param string[] $parameters
     */
    private function log(string $message, string $priority = LogLevel::INFO, array $parameters = []): void
    {
        $this->logger->log($priority, $message, $parameters);
    }

    private function startTimingTheParsePhase(): void
    {
        $this->stopwatch->start('parser.parse');
    }
}
