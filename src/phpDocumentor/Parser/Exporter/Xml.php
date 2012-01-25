<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @category  phpDocumentor
 * @package   Parser\Exporter\Xml
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

/**
 * Class responsible for writing the results of the Reflection to a single
 * Intermediate Structure file in XML.
 *
 * @category phpDocumentor
 * @package  Parser\Exporter\Xml
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://phpdoc.org
 */
class phpDocumentor_Parser_Exporter_Xml extends phpDocumentor_Parser_Exporter_Abstract
{
    /** @var DOMDocument $xml Document containing the collected data */
    protected $xml = null;

    /**
     * Initializes this exporter.
     *
     * @return void
     */
    public function initialize()
    {
        $this->xml = new DOMDocument('1.0', 'utf-8');
        $this->xml->formatOutput = true;
        $document_element = new DOMElement('project');
        $this->xml->appendChild($document_element);

        $document_element->setAttribute('version', phpDocumentor_Core_Abstract::VERSION);
        $document_element->setAttribute('title', $this->parser->getTitle());
    }

    public function finalize()
    {
        // filter all undesired tags
        if (count($this->parser->getIgnoredTags()) > 0) {
            $query = '//tag[@name=\''
                . implode('\']|//tag[@name=\'', $this->parser->getIgnoredTags())
                . '\']';
            $xpath = new DOMXPath($this->xml);
            $qry = $xpath->query($query);

            /** @var DOMElement $item */
            for ($i = 0; $i < $qry->length; $i++) {
                $qry->item($i)->parentNode->removeChild($qry->item($i));
            }
        }

        $this->buildPackageTree($this->xml);
        $this->buildNamespaceTree($this->xml);
        $this->buildMarkerList($this->xml);
        $this->filterVisibility($this->xml, $this->parser->getVisibility());
    }

    /**
     * Renders the reflected file to a structure file.
     *
     * @param phpDocumentor_Reflection_File $file File to export.
     *
     * @return void
     */
    public function export(phpDocumentor_Reflection_File $file)
    {
        $object = new phpDocumentor_Parser_Exporter_Xml_File();
        $object->include_source = $this->include_source;
        $object->export($this->xml->documentElement, $file);
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
        $this->log('Collecting all packages');
        $xpath = new DOMXPath($dom);
        $packages = array();
        $qry = $xpath->query('//@package');
        for ($i = 0; $i < $qry->length; $i++) {
            if (isset($packages[$qry->item($i)->nodeValue])) {
                continue;
            }

            $packages[$qry->item($i)->nodeValue] = true;
        }

        $packages = $this->generateNamespaceTree(array_keys($packages));
        $this->generateNamespaceElements(
            $packages, $dom->documentElement, 'package'
        );
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
        foreach ($this->parser->getMarkers() as $marker) {
            $node = new DOMElement('marker', strtolower($marker));
            $dom->documentElement->appendChild($node);
        }
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
     * @param string     $node_name      the name of the summary element.
     *
     * @return void
     */
    protected function generateNamespaceElements($namespaces, $parent_element,
        $node_name = 'namespace'
    ) {
        foreach ($namespaces as $name => $sub_namespaces) {
            $node = new DOMElement($node_name);
            $parent_element->appendChild($node);
            $node->setAttribute('name', $name);
            $this->generateNamespaceElements($sub_namespaces, $node, $node_name);
        }
    }

    protected function filterVisibility($dom, $visibility)
    {
        $visibilityQry = '//*[';
        $accessQry = '//tag[@name=\'access\' and (';
        foreach ($visibility as $key => $vis) {
            $visibilityQry .= '(@visibility!=\''.$vis.'\')';
            $accessQry .= '@description!=\''.$vis.'\'';

            if (($key + 1) < count($visibility)) {
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
            if (($node->nodeName == 'tag')
                && ($node->parentNode->parentNode->parentNode)
            ) {
                $remove = $node->parentNode->parentNode;
                $node->parentNode->parentNode->parentNode->removeChild($remove);
            } else {
                $node->parentNode->removeChild($node);
            }
        }
    }

    /**
     * Returns the DOMDocument for this exporter.
     *
     * @return DOMDocument
     */
    public function getDomDocument()
    {
        return $this->xml;
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