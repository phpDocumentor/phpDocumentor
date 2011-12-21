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
 * Class responsible for writing the results of the Reflection of a single
 * source file to disk.
 *
 * @category DocBlox
 * @package  Parser
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://docblox-project.org
 */
class DocBlox_Parser_Exporter_Xml extends DocBlox_Parser_Abstract
{
    /** @var string Title of this project */
    protected $title = '';

    /** @var DOMDocument $xml Document containing the collected data */
    protected $xml = null;

    /** @var bool Whether to include the file's source in the export */
    protected $include_source = false;

    /**
     * Construct the object with the location where to write the structure file(s).
     *
     * @param string $title Title for this documentation.
     */
    public function __construct($title)
    {
        $this->title = $title;
    }

    /**
     * Returns the DOMDocument for this exporter.
     *
     * @return DOMDocument|null
     */
    public function getDomDocument()
    {
        return $this->xml;
    }

    /**
     * Sets whether the source of the file needs to be included in the export.
     *
     * @param $include_source
     */
    public function setIncludeSource($include_source)
    {
        $this->include_source = $include_source;
    }

    /**
     * Initializes this exporter.
     *
     * @return void
     */
    public function initialize()
    {
        $version = DocBlox_Core_Abstract::VERSION;

        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true;
        $dom->loadXML(
            <<<XML
                        <project version="$version" title="{$this->title}">
            </project>
XML
        );

        $this->xml = $dom;
    }

    /**
     * Finalizes this exporter; performs cleaning operations.
     *
     * @return void
     */
    public function finalize()
    {

    }

    /**
     * Renders the reflected file to a structure file.
     *
     * @param DocBlox_Reflection_File $file File to export.
     *
     * @return void
     */
    public function export(DocBlox_Reflection_File $file)
    {
        $xml = new SimpleXMLElement(
            '<file path="' . ltrim($file->getFilename(), './') . '" hash="'
                    . $file->getHash() . '"></file>'
        );
        $this->addDocblockToSimpleXmlElement(
            $xml, $file->getDocBlock(), $file->getDefaultPackageName()
        );

        // add markers
        foreach ($file->getMarkers() as $marker) {
            if (!isset($xml->markers)) {
                $xml->addChild('markers');
            }

            $marker_obj = $xml->markers->addChild(
                strtolower($marker[0]),
                htmlspecialchars(trim($marker[1]))
            );
            $marker_obj->addAttribute('line', $marker[2]);
        }

        foreach ($file->getParseErrors() as $marker) {
            if (!isset($xml->parse_markers)) {
                $xml->addChild('parse_markers');
            }

            $marker_obj = $xml->parse_markers->addChild(
                strtolower($marker[0]),
                htmlspecialchars(trim($marker[1]))
            );
            $marker_obj->addAttribute('line', $marker[2]);
        }

        // add namespace aliases
        foreach ($file->getNamespaceAliases() as $alias => $namespace) {
            $alias_obj = $xml->addChild('namespace-alias', $namespace);
            $alias_obj->addAttribute('name', $alias);
        }

        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->loadXML(trim($xml->asXML()));

        /** @var DocBLox_Reflection_Include $include */
        foreach ($file->getIncludes() as $include) {
            $this->mergeXmlToDomDocument($dom, trim($include->__toXml()));
        }

        /** @var DocBlox_Reflection_Constant $constant */
        foreach ($file->getConstants() as $constant) {
            $constant->setDefaultPackageName($xml['package']);
            $this->mergeXmlToDomDocument($dom, trim($constant->__toXml()));
        }

        /** @var DocBlox_Reflection_Function $function */
        foreach ($file->getFunctions() as $function) {
            $function->setDefaultPackageName($xml['package']);
            $this->mergeXmlToDomDocument($dom, trim($function->__toXml()));
        }

        /** @var DocBlox_Reflection_Interface $interface */
        foreach ($file->getInterfaces() as $interface) {
            $this->mergeXmlToDomDocument($dom, trim($interface->__toXml()));
        }
        foreach ($file->getClasses() as $class) {
            $this->mergeXmlToDomDocument($dom, trim($class->__toXml()));
        }

        $this->xml->documentElement->appendChild(
            $this->xml->importNode($dom->childNodes->item(0), true)
        );

        // if we want to include the source for each file; append a new
        // element 'source' which contains a compressed, encoded version
        // of the source
        if ($this->include_source) {
            $dom->documentElement->appendChild(
                new DOMElement(
                    'source',
                    base64_encode(gzcompress($file->getContents()))
                )
            );
        }

    }

    /**
     * Helper used to merge a given XML string into a given DOMDocument.
     *
     * @param DOMDocument $origin Destination to merge the XML into.
     * @param string      $xml    The XML to merge with the document.
     *
     * @return void
     */
    protected function mergeXmlToDomDocument(DOMDocument $origin, $xml)
    {
        $dom_arguments = new DOMDocument();
        $dom_arguments->loadXML(trim($xml));

        $this->mergeDomDocuments($origin, $dom_arguments);
    }

    /**
     * Adds the DocBlock XML definition to the given SimpleXMLElement.
     *
     * @param SimpleXMLElement                 $xml
     *     SimpleXMLElement to be appended to
     * @param DocBlox_Reflection_DocBlock|null $docblock             DocBlock to
     *     append onto $xml
     * @param string                           $default_package_name Name of the
     *     default package to use; should this DocBlock not have any.
     *
     * @return void
     */
    protected function addDocblockToSimpleXmlElement(
        SimpleXMLElement $xml, $docblock,
        $default_package_name
    ) {
        $package = '';
        $subpackage = '';

        if ($docblock) {
            if (!isset($xml->docblock)) {
                $xml->addChild('docblock');
            }

            $xml->docblock->description = $docblock->getShortDescription();
            $xml->docblock->{'long-description'} = $docblock
                ->getLongDescription()->getFormattedContents();


            /** @var DocBlox_Reflection_Docblock_Tag $tag */
            foreach ($docblock->getTags() as $tag) {
                $tag_object = $xml->docblock->addChild('tag');

                // custom attached member variable, see line 51
                if (isset($docblock->line_number)) {
                    $tag_object['line'] = $docblock->line_number;
                }

                $this->d~ispatch(
                    'reflection.docblock.tag.export',
                    array(
                        'object' => $tag,
                        'xml' => $tag_object
                    )
                );

                if ($tag->getName() == 'package') {
                    $package = $tag->getDescription();
                }

                if ($tag->getName() == 'subpackage') {
                    $subpackage = $tag->getDescription();
                }
            }
        }

        // create a new 'meta-package' shaped like a namespace
        $xml['package'] = str_replace(
            array('.', '_'),
            '\\',
                $package . ($subpackage ? '\\' . $subpackage : '')
        );

        if ((string)$xml['package'] == '') {
            $xml['package'] = $default_package_name;
        }
    }

    /**
     * Helper method which merges a $document into $origin.
     *
     * @param DOMDocument $origin   The document to accept the changes.
     * @param DOMDocument $document The changes which are to be merged into
     *     the origin.
     *
     * @return void
     */
    protected function mergeDomDocuments(
        DOMDocument $origin,
        DOMDocument $document
    )
    {
        $xpath = new DOMXPath($document);
        $qry = $xpath->query('/*');
        for ($i = 0; $i < $qry->length; $i++) {
            $origin->documentElement->appendChild(
                $origin->importNode($qry->item($i), true)
            );
        }
    }

    /**
     * Returns the XML contents of this export.
     *
     * @return string
     */
    public function getContents()
    {
        return $this->xml->saveXML();
    }
}
