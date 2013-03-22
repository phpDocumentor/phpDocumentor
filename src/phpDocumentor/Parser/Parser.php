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

use phpDocumentor\Parser\Exception\FilesNotFoundException;
use phpDocumentor\Reflection\FileReflector;
use phpDocumentor\Fileset\Collection;
use phpDocumentor\Event\Dispatcher;
use phpDocumentor\Parser\Event\PreFileEvent;
use phpDocumentor\Parser\Exporter\ExporterAbstract;
use phpDocumentor\Parser\Exporter\Xml\Xml as DefaultExporter;

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
class Parser extends ParserAbstract
{
    /** @var string the title to use in the header */
    protected $title = '';

    /** @var string the name of the default package */
    protected $default_package_name = 'Default';

    /**
     * @var \DOMDocument|null if any structure.xml was at the target location it
     *                       is stored for comparison
     */
    protected $existing_xml = null;

    /**
     * @var bool whether we force a full re-parse, independent of existing_xml
     *           is set
     */
    protected $force = false;

    /** @var bool whether to execute a PHPLint on every file */
    protected $validate = false;

    /** @var string[] which markers (i.e. TODO or FIXME) to collect */
    protected $markers = array('TODO', 'FIXME');

    /** @var string[] which tags to ignore */
    protected $ignored_tags = array();

    /** @var string target location's root path */
    protected $path = null;

    /**
     * Array of visibility modifiers that should be adhered to when generating
     * the documentation
     *
     * @var array
     */
    protected $visibility = array('public', 'protected', 'private');

    /** @var Exporter\ExporterAbstract */
    protected $exporter = null;

    /** @var string The encoding in which the files are encoded */
    protected $encoding = 'utf-8';

    /**
     * List of CRC hashes for each file.
     *
     * If a pre-existing structure file is found than a list of hashes is retrieved to verify whether the contents
     * of that file has changed. If it has changed than the parser will analyze the file; otherwise it will re-use
     * the existing definition.
     *
     * @var string[]
     */
    protected $hashes = array();

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
        $default_encoding = ini_get('zend.script_encoding');
        if ($default_encoding) {
            $this->encoding = $default_encoding;
        }
    }

    /**
     * Sets the title for this project.
     *
     * @param string $title The intended title for this project.
     *
     * @api
     *
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Returns the HTML text which is found at the title's position.
     *
     * @api
     *
     * @return null|string
     */
    public function getTitle()
    {
        return $this->title;
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
     * To prevent incompatibilities we force a full rebuild if the version of
     * phpDocumentor does not equal the structure's version.
     *
     * @api
     *
     * @return bool
     */
    public function isForced()
    {
        $is_version_unequal = (($this->getExistingXml())
           && ($this->getExistingXml()->documentElement->getAttribute('version')
               != \phpDocumentor\Application::VERSION));

        if ($is_version_unequal) {
            $this->log(
                'Version of phpDocumentor has changed since the last build; '
                . 'forcing a full re-build'
            );
        }

        return $this->force || $is_version_unequal;
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
     * @param string[] $ignored_tags A list of tags to ignore.
     *
     * @api
     *
     * @return void
     */
    public function setIgnoredTags(array $ignored_tags)
    {
        $this->ignored_tags = $ignored_tags;
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
        return $this->ignored_tags;
    }

    /**
     * Imports an existing XML source to enable incremental parsing.
     *
     * @param string|null $xml XML contents, or filename of an XML file, or null if no source is available.
     *
     * @todo this method should be part of the exporter; it should be the exporter who determines whether an entry
     *     needs to be reparsed since the exporter is aware of the format of the exported file
     *
     * @api
     *
     * @throws \InvalidArgumentException if the parameter represents a file that is unreadable or a malformed
     *     XML string.
     *
     * @return void
     */
    public function setExistingXml($xml)
    {
        $dom = null;
        if ($xml !== null) {
            $this->existing_xml = null;
            if (substr(trim($xml), 0, 5) != '<?xml') {
                if (!file_exists($xml) || !is_readable($xml)) {
                    $this->log('No existing structure file found, executing a full pass');
                    return;
                }
                $xml = file_get_contents($xml);
            }

            libxml_use_internal_errors(true);
            $dom = new \DOMDocument('1.0', 'utf-8');
            $result = $dom->loadXML($xml);

            // if the loadXml method returns false than there is something wrong with the document; report and do a full
            // run.
            if (!$result) {
                /** @var \LibXMLError $error */
                foreach (libxml_get_errors() as $error) {
                    $this->log($error->message, LOG_ERR);
                }
                libxml_clear_errors();
                $this->log('Existing structure content is corrupt; performing a full parsing session', LOG_ERR);

                $dom = null;
            }
        }

        $this->existing_xml = $dom;
    }

    /**
     * Returns the existing data structure as DOMDocument.
     *
     * @api
     *
     * @return \DOMDocument|null
     */
    public function getExistingXml()
    {
        return $this->existing_xml;
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
     * Sets the exporter that is used to convert the reflected information into output.
     *
     * After each file is parsed is this Exporter invoked to build an Abstract Syntax Tree. The results from this
     * export process is returned at the end of the file processing.
     *
     * This method is set to protected as it is part of a refactoring to move to a different system of handling
     * exporting and importing.
     *
     * @param ExporterAbstract $exporter
     *
     * @return void
     */
    protected function setExporter(ExporterAbstract $exporter)
    {
        $this->exporter = $exporter;
    }

    /**
     * Returns the exporter for this parser.
     *
     * If no exporter has been set beforehand does this method create the default exporter.
     *
     * @return ExporterAbstract
     */
    public function getExporter()
    {
        if (!$this->exporter) {
            $this->exporter = new DefaultExporter($this);
        }

        return $this->exporter;
    }

    /**
     * Sets the name of the default package.
     *
     * @param string $default_package_name Name used to categorize elements
     *  without an @package tag.
     *
     * @return void
     */
    public function setDefaultPackageName($default_package_name)
    {
        $this->default_package_name = $default_package_name;
    }

    /**
     * Returns the name of the default package.
     *
     * @return string
     */
    public function getDefaultPackageName()
    {
        return $this->default_package_name;
    }

    /**
     * Iterates through the given files and builds the structure.xml file.
     *
     * @param Collection $files          A files container to parse.
     * @param bool       $include_source Whether to include the source in the generated output.
     *
     * @api
     *
     * @throws Exception if no files were found.
     *
     * @return bool|string
     */
    public function parseFiles(Collection $files, $include_source = false)
    {
        $timer = microtime(true);
        $paths = $this->getFilenames($files);

        if (!$this->isForced()) {
            $this->hashes = $this->loadHashes($this->getExistingXml());
        }

        $this->log('  Project root is:  ' . $files->getProjectRoot());
        $this->log(
            '  Ignore paths are: ' . implode(', ', $files->getIgnorePatterns()->getArrayCopy())
        );

        $this->getExporter()->initialize();
        foreach ($paths as $file) {
            Dispatcher::getInstance()->dispatch('parser.file.pre', PreFileEvent::createInstance($this)->setFile($file));
            $this->parseFile($file, $include_source);
        }
        $this->getExporter()->finalize();

        $this->log('Elapsed time to parse all files: ' . round(microtime(true) - $timer, 2) . 's');
        $this->log('Peak memory usage: '. round(memory_get_peak_usage() / 1024 / 1024, 2) . 'M');

        return $this->getExporter()->getContents();
    }

    /**
     * @param \phpDocumentor\Fileset\Collection $files
     *
     * @throws FilesNotFoundException if no files were found.
     *
     * @return \string[]
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
     * Runs a file through the static reflectors, generates an XML file element
     * and returns it.
     *
     * @param string $filename       The filename to parse.
     * @param bool   $include_source whether to include the source in the
     *  generated output.
     *
     * @api
     *
     * @return void
     */
    public function parseFile($filename, $include_source = false)
    {
        $this->log('Starting to parse file: ' . $filename);

        $dispatched = false;
        try {
            $file = new FileReflector($filename, $this->doValidation(), $this->getEncoding());
            $file->setDefaultPackageName($this->getDefaultPackageName());

            if (class_exists('phpDocumentor\Event\Dispatcher')) {
                Dispatcher::getInstance()->addListener('parser.log', array($file, 'addParserMarker'));
                $dispatched = true;
            }

            $file->setMarkers($this->getMarkers());
            $file->setFilename($this->getRelativeFilename($filename));

            $path = ltrim($file->getFilename(), './');

            if (isset($this->hashes[$path]) && ($this->hashes[$path] == $file->getHash())) {
                $xpath = new \DOMXPath($this->getExistingXml());
                $qry = $xpath->query('/project/file[@path=\'' . $path . '\' and @hash=\'' . $file->getHash() . '\']');

                $this->getExporter()->getDomDocument()->documentElement->appendChild(
                    $this->getExporter()->getDomDocument()->importNode($qry->item(0), true)
                );

                $this->log('>> File has not changed since last build, re-using the old definition');
            } else {
                $this->log('Exporting file: ' . $filename);

                $file->process();
                $this->getExporter()->setIncludeSource($include_source);
                $this->getExporter()->export($file);
            }
        } catch (Exception $e) {
            $this->log(
                '  Unable to parse file "' . $filename . '", an error was detected: ' . $e->getMessage(),
                \phpDocumentor\Plugin\Core\Log::ALERT
            );
        }

        // Disconnects the dispatcher here so if any error occurred, it still removes the event
        if ($dispatched && isset($file)) {
            Dispatcher::getInstance()->removeListener('parser.log', array($file, 'addParserMarker'));
        }

        $this->log(
            '>> Memory after processing of file: ' . number_format(memory_get_usage()) . ' bytes',
            \phpDocumentor\Plugin\Core\Log::DEBUG
        );
    }

    /**
     * Retrieves a list of filenames with their content hash from the existing AST.
     *
     * @param \DOMDocument|null $ast Either the DOMDocument representing the AST or null to indicate this is a new run.
     *
     * @throws \InvalidArgumentException if an invalid object is passed.
     *
     * @codeCoverageIgnore should be moved to the exporter
     *
     * @return string[] An associative array where the key represents the filename and the value the hash of that file.
     */
    protected function loadHashes($ast)
    {
        $result = array();
        if (!$ast) {
            return $result;
        }

        if (!$ast instanceof \DOMDocument) {
            throw new \InvalidArgumentException('Invalid argument provided, expected a DOMDocument instance.');
        }

        $this->log('Loading file hashes for incremental parsing');

        $xpath = new \DOMXPath($ast);
        $qry = $xpath->query('/project/file');
        for ($i = 0; $i < $qry->length; $i++) {
            $file = $qry->item($i);

            $path = $file->attributes->getNamedItem('path')->nodeValue;
            $hash = $file->attributes->getNamedItem('hash')->nodeValue;

            $result[$path] = $hash;
        }

        return $result;
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
}
