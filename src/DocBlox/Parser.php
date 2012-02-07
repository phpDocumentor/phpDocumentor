<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category  DocBlox
 * @package   Parser
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://docblox-project.org
 */

/**
 * Class responsible for parsing the given file or files to the intermediate
 * structure file.
 *
 * This class can be used to parse one or more files to the intermediate file
 * format for further processing.
 *
 * Example of use:
 *
 *     $files = new DocBlox_Parser_Files();
 *     $files->addDirectories(getcwd());
 *     $parser = new DocBlox_Parser();
 *     $parser->setPath($files->getProjectRoot());
 *     echo $parser->parseFiles($files);
 *
 * @category DocBlox
 * @package  Parser
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://docblox-project.org
 */
class DocBlox_Parser extends DocBlox_Parser_Abstract
{
    /** @var string the title to use in the header */
    protected $title = '';

    /** @var string the name of the default package */
    protected $default_package_name = 'Default';

    /**
     * @var DOMDocument|null if any structure.xml was at the target location it
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

    /** @var DocBlox_Parser_Exporter_Abstract */
    protected $exporter = null;

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
     * Get the path of the source code
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
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
     * DocBlox does not equal the structure's version.
     *
     * @api
     *
     * @return bool
     */
    public function isForced()
    {
        $is_version_unequal = (($this->getExistingXml())
           && ($this->getExistingXml()->documentElement->getAttribute('version')
               != DocBlox_Core_Abstract::VERSION));

        if ($is_version_unequal) {
            $this->log(
                'Version of DocBlox has changed since the last build; '
                . 'forcing a full re-build'
            );
        }

        return $this->force || $is_version_unequal;
    }

    /**
     * Sets whether to run PHPLint on every file.
     *
     * PHPLint has a huge performance impact on the execution of DocBlox and
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
     * @param string|null $xml XML contents if a source exists, otherwise null.
     *
     * @api
     *
     * @return void
     */
    public function setExistingXml($xml)
    {
        $dom = null;
        if ($xml !== null) {
            if (substr(trim($xml), 0, 5) != '<?xml') {
                $xml = (is_readable($xml))
                    ? file_get_contents($xml)
                    : '<?xml version="1.0" encoding="utf-8"?><docblox></docblox>';
            }

            $dom = new DOMDocument();
            $dom->loadXML($xml);
        }

        $this->existing_xml = $dom;
    }

    /**
     * Returns the existing data structure as DOMDocument.
     *
     * @api
     *
     * @return DOMDocument|null
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
     * @throws InvalidArgumentException if file is not in the project root.
     *
     * @return string
     */
    public function getRelativeFilename($filename)
    {
        // strip path from filename
        $result = ltrim(substr($filename, strlen($this->path)), '/');
        if ($result === '') {
            throw new InvalidArgumentException(
                'File is not present in the given project path: ' . $filename
            );
        }

        return $result;
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
        $this->debug('Starting to parse file: ' . $filename);

        $dispatched = false;
        try {
            $file = new DocBlox_Reflection_File($filename, $this->doValidation());
            $file->setDefaultPackageName($this->getDefaultPackageName());

            if (self::$event_dispatcher !== null) {
                self::$event_dispatcher->connect(
                    'parser.log',
                    array($file, 'addParserMarker')
                );
            }
            $dispatched = true;

            $file->setMarkers($this->getMarkers());
            $file->setFilename($this->getRelativeFilename($filename));
            $file->setName($this->getRelativeFilename($filename));

            // if an existing structure exists; and we do not force re-generation;
            // re-use the old definition if the hash differs
            if (($this->getExistingXml() !== null) && (!$this->isForced())) {
                $xpath = new DOMXPath($this->getExistingXml());

                // try to find the file with it's hash
                /** @var DOMNodeList $qry */
                $qry = $xpath->query(
                    '/project/file[@path=\'' . ltrim($file->getName(), './')
                    . '\' and @hash=\'' . $file->getHash() . '\']'
                );

                // if an existing entry who matches the file, then re-use
                if ($qry->length > 0) {
                    $this->exporter->getDomDocument()->documentElement->appendChild(
                        $this->exporter->getDomDocument()->importNode(
                            $qry->item(0), true
                        )
                    );

                    $this->log(
                        '>> File has not changed since last build, re-using the '
                        . 'old definition'
                    );
                } else {
                    $this->log('Exporting file: ' . $filename);

                    $file->process();
                    $this->exporter->setIncludeSource($include_source);
                    $this->exporter->export($file);
                }
            } else {
                $this->log('Exporting file: ' . $filename);

                $file->process();
                $this->exporter->setIncludeSource($include_source);
                $this->exporter->export($file);
            }
        } catch (Exception $e) {
            $this->log(
                '>>  Unable to parse file, an error was detected: '
                . $e->getMessage(),
                Zend_Log::ALERT
            );
            $this->debug(
                'Unable to parse file "' . $filename . '", an error was detected: '
                . $e->getMessage()
            );
        }

        //disconnects the dispatcher here so if any error occured, it still
        // removes the event
        if ($dispatched && self::$event_dispatcher !== null) {
            self::$event_dispatcher->disconnect(
                'parser.log',
                array($file, 'addParserMarker')
            );
        }

        $this->debug(
            '>> Memory after processing of file: '
            . number_format(memory_get_usage()) . ' bytes'
        );
        $this->debug('>> Parsed file');
    }

    /**
     * Iterates through the given files and builds the structure.xml file.
     *
     * @param DocBlox_Parser_Files $files          A files container to parse.
     * @param bool                 $include_source whether to include the source
     *  in the generated output..
     *
     * @api
     *
     * @return bool|string
     */
    public function parseFiles(DocBlox_Parser_Files $files, $include_source = false)
    {
        $timer = microtime(true);

        $this->exporter = new DocBlox_Parser_Exporter_Xml($this);
        $this->exporter->initialize();

        $paths = $files->getFiles();
        $this->log('Starting to process ' . count($paths) . ' files');
        $this->log('  Project root is:  ' . $files->getProjectRoot());
        $this->log(
            '  Ignore paths are: ' . implode(', ', $files->getIgnorePatterns())
        );

        if (count($paths) < 1) {
            throw new DocBlox_Parser_Exception(
                'No files were found',
                DocBlox_Parser_Exception::NO_FILES_FOUND
            );
        }

        $file_count = count($paths);
        foreach ($paths as $key => $file) {
            $this->dispatch(
                'parser.file.pre',
                array('file' => $file, 'progress' => array($key +1, $file_count))
            );

            $this->parseFile($file, $include_source);
        }

        $this->exporter->finalize();

        $this->log('--');
        $this->log(
            'Elapsed time to parse all files: '
            . round(microtime(true) - $timer, 2) . 's'
        );

        $this->log(
            'Peak memory usage: '
            . round(memory_get_peak_usage() / 1024 / 1024, 2) . 'M'
        );

        return $this->exporter->getContents();
    }

    /**
     * Sets the name of the defautl package.
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

}