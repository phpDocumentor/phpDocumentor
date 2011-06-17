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
 * Core class responsible for parsing the given files to a structure.xml file.
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

    /** @var string target location's root path */
    protected $path = null;

    /**
     * Array of visibility modifiers that should be adhered to when generating
     * the documentation
     *
     * @var array
     */
    protected $visibility = array('public', 'protected', 'private');

    /**
     * Sets the title for this project.
     *
     * @param string $title The intended title for this project.
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
     * @return void
     */
    public function setValidate($validate)
    {
        $this->validate = $validate;
    }

    /**
     * Returns whether we want to run PHPLint on every file.
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
     * @return void
     */
    public function setMarkers(array $markers)
    {
        $this->markers = $markers;
    }

    /**
     * Returns the list of markers.
     *
     * @return string[]
     */
    public function getMarkers()
    {
        return $this->markers;
    }

    /**
     * Imports an existing XML source to enable incremental parsing.
     *
     * @param string|null $xml XML contents if a source exists, otherwise null.
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
     * @return void
     */
    public function setVisibility($visibility)
    {
        if ('' != $visibility) {
            $this->visibility = explode(',', $visibility);
        }
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
     * @param string $filename The filename to parse.
     *
     * @return string|bool The XML element or false if none could be made.
     */
    function parseFile($filename)
    {
        $this->log('Starting to parse file: ' . $filename);
        $this->debug('Starting to parse file: ' . $filename);
        $result = null;

        try {
            $file = new DocBlox_Reflection_File($filename, $this->doValidation());
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
                    $new_dom = new DOMDocument('1.0', 'utf-8');
                    $new_dom->appendChild($new_dom->importNode($qry->item(0), true));
                    $result = $new_dom->saveXML();

                    $this->log(
                        '>> File has not changed since last build, re-using the '
                        . 'old definition'
                    );
                }
            }

            // if no result has been obtained; process the file
            if ($result === null) {
                $file->process();
                $result = $file->__toXml();
            }
        } catch (Exception $e)
        {
            $this->log(
                '>>  Unable to parse file, an error was detected: '
                . $e->getMessage(),
                Zend_Log::ALERT
            );
            $this->debug(
                'Unable to parse file "' . $filename . '", an error was detected: '
                . $e->getMessage()
            );
            $result = false;
        }

        $this->debug(
            '>> Memory after processing of file: '
            . number_format(memory_get_usage()) . ' bytes'
        );
        $this->debug('>> Parsed file');

        return $result;
    }

    /**
     * Generates a hierarchical array of namespaces with their singular name
     * from a single level list of namespaces with their full name.
     *
     * @param array $namespaces the list of namespaces as retrieved from the xml.
     *
     * @return array
     */
    protected function generateNamespaceTree($namespaces)
    {
        sort($namespaces);

        $result = array();
        foreach ($namespaces as $namespace) {
            $namespace_list = explode('\\', $namespace);

            $node = &$result;
            foreach ($namespace_list as $singular) {
                if (!isset($node[$singular])) {
                    $node[$singular] = array();
                }

                $node = &$node[$singular];
            }
        }

        return $result;
    }

    /**
     * Recursive method to create a hierarchical set of nodes in the dom.
     *
     * @param array[]    $namespaces     the list of namespaces to process.
     * @param DOMElement $parent_element the node to receive the children of
     *                                   the above list.
     *
     * @return void
     */
    protected function generateNamespaceElements($namespaces, $parent_element)
    {
        foreach ($namespaces as $name => $sub_namespaces) {
            $node = new DOMElement('namespace');
            $parent_element->appendChild($node);
            $node->setAttribute('name', $name);
            $this->generateNamespaceElements($sub_namespaces, $node);
        }
    }

    /**
     * Iterates through the given files and builds the structure.xml file.
     *
     * @param DocBlox_Parser_Files $files A files container to parse.
     *
     * @return bool|string
     */
    public function parseFiles(DocBlox_Parser_Files $files)
    {
        $timer = microtime(true);

        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true;
        $dom->loadXML(
            '<project version="' . DocBlox_Core_Abstract::VERSION . '" '
            . 'title="' . addslashes($this->getTitle()) . '"></project>'
        );

        $paths = $files->getFiles();
        $this->log('Starting to process ' . count($paths) . ' files') . PHP_EOL;
        $this->log('  Project root is:  ' . $files->getProjectRoot()) . PHP_EOL;
        $this->log('  Ignore paths are: ' . implode(', ', $files->getIgnorePatterns())) . PHP_EOL;
        if (count($paths) < 1)
        {
            throw new DocBlox_Parser_Exception(
                'No files were found',
                DocBlox_Parser_Exception::NO_FILES_FOUND
            );
        }

        foreach ($paths as $file) {
            $xml = $this->parseFile($file);
            if ($xml === false) {
                continue;
            }

            $dom_file = new DOMDocument();
            $dom_file->loadXML(trim($xml));

            // merge generated XML document into the main document
            $xpath = new DOMXPath($dom_file);
            $qry = $xpath->query('/*');
            for ($i = 0; $i < $qry->length; $i++) {
                $dom->documentElement->appendChild(
                    $dom->importNode($qry->item($i), true)
                );
            }
        }

        $this->buildPackageTree($dom);
        $this->buildNamespaceTree($dom);
        $this->buildMarkerList($dom);

        $xml = $dom->saveXML();

        // Visibility rules
        $this->log('--');
        $this->log('Applying visibility rules');
        $dom = new DOMDocument();
        $dom->loadXML($xml);

        $visibilityQry = '//*[';
        $accessQry = '//tag[@name=\'access\' and (';
        foreach ($this->visibility as $key => $vis) {
            $visibilityQry .= '(@visibility!=\''.$vis.'\')';
            $accessQry .= '@description!=\''.$vis.'\'';

            if (($key + 1) < count($this->visibility)) {
                $visibilityQry .= ' and ';
                $accessQry .= ' and ';
            }

        }
        $visibilityQry .= ']';
        $accessQry .= ')]';

        $qry = '('.$visibilityQry.') | ('.$accessQry.')';

        $xpath = new DOMXPath($dom);
        $nodes = $xpath->query($qry);

        foreach ($nodes as $node) {

            if ($node->nodeName == 'tag' && $node->parentNode->parentNode->parentNode) {
                $remove = $node->parentNode->parentNode;
                $node->parentNode->parentNode->parentNode->removeChild($remove);
            } else {
                $node->parentNode->removeChild($node);
            }
        }
        $xml = $dom->saveXML();

        $this->log('--');
        $this->log(
            'Elapsed time to parse all files: '
            . round(microtime(true) - $timer, 2) . 's'
        );

        $this->log(
            'Peak memory usage: '
            . round(memory_get_peak_usage() / 1024 / 1024, 2) . 'M'
        );

        return $xml;
    }

    /**
     * Collects all packages and subpackages, and adds a new section in the
     * DOM to provide an overview.
     *
     * @param DOMDocument $dom Packages are extracted and a summary inserted
     *                         in this object.
     *
     * @return void
     */
    protected function buildPackageTree(DOMDocument $dom)
    {
        // collect all packages and store them in the XML
        $this->log('Collecting all packages');
        $packages = array('' => '');

        // at least insert a default package
        $node = new DOMElement('package');
        $dom->documentElement->appendChild($node);
        $node->setAttribute('name', '');

        $xpath = new DOMXPath($dom);
        $qry = $xpath->query(
            '/project/file/class/docblock/tag[@name="package"]'
            . '|/project/file/interface/docblock/tag[@name="package"]'
            . '|/project/file/docblock/tag[@name="package"]'
        );

        // iterate through all packages
        for ($i = 0; $i < $qry->length; $i++) {
            $package_name = $qry->item($i)->attributes
                ->getNamedItem('description')->nodeValue;
            if (isset($packages[$package_name])) {
                continue;
            }

            $packages[$package_name] = array();

            // find all subpackages
            $qry2 = $xpath->query(
                '//docblock/tag[@name="package" and @description="'
                . $package_name . '"]/../tag[@name="subpackage"]'
            );
            for ($i2 = 0; $i2 < $qry2->length; $i2++) {
                $packages[$package_name][] = $qry2->item($i2)->attributes
                    ->getNamedItem('description')->nodeValue;
            }
            $packages[$package_name] = array_unique($packages[$package_name]);

            // create package XMl and subpackages
            $node = new DOMElement('package');
            $dom->documentElement->appendChild($node);
            $node->setAttribute('name', $package_name);
            foreach ($packages[$package_name] as $subpackage) {
                $node->appendChild(new DOMElement('subpackage', $subpackage));
            }
        }
    }

    /**
     * Collects all namespaces and sub-namespaces, and adds a new section in
     * the DOM to provide an overview.
     *
     * @param DOMDocument $dom Namespaces are extracted and a summary inserted
     *                         in this object.
     *
     * @return void
     */
    protected function buildNamespaceTree(DOMDocument $dom)
    {
        $this->log('Collecting all namespaces');
        $xpath = new DOMXPath($dom);
        $namespaces = array();
        $qry = $xpath->query('//@namespace');
        for ($i = 0; $i < $qry->length; $i++) {
            if (isset($namespaces[$qry->item($i)->nodeValue])) {
                continue;
            }

            $namespaces[$qry->item($i)->nodeValue] = true;
        }

        $namespaces = $this->generateNamespaceTree(array_keys($namespaces));
        $this->generateNamespaceElements($namespaces, $dom->documentElement);
    }

    /**
     * Retrieves a list of all marker types and adds them to the XML for
     * easy referencing.
     *
     * @param DOMDocument $dom Markers are extracted and a summary inserted in
     *                         this object.
     *
     * @return void
     */
    protected function buildMarkerList(DOMDocument $dom)
    {
        $this->log('Collecting all marker types');
        foreach ($this->getMarkers() as $marker) {
            $node = new DOMElement('marker', strtolower($marker));
            $dom->documentElement->appendChild($node);
        }
    }

}