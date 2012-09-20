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

namespace phpDocumentor\Parser\Exporter\Xml;

use phpDocumentor\Parser\Exporter\ExporterAbstract;
use phpDocumentor\Reflection\FileReflector;

/**
 * Class responsible for writing the results of the Reflection to a single
 * Intermediate Structure file in XML.
 */
class Xml extends ExporterAbstract
{
    /** @var \DOMDocument $xml Document containing the collected data */
    protected $xml = null;

    /**
     * Initializes this exporter.
     *
     * @return void
     */
    public function initialize()
    {
        $this->xml = new \DOMDocument('1.0', 'utf-8');
        $this->xml->formatOutput = true;
        $document_element = new \DOMElement('project');
        $this->xml->appendChild($document_element);

        $document_element->setAttribute(
            'version', \phpDocumentor\Application::VERSION
        );
        $document_element->setAttribute('title', $this->parser->getTitle());
    }

    /**
     * Finalizes the processing and executing all post-processing actions.
     *
     * This method is responsible for extracting and manipulating the data that
     * is global to the project, such as:
     *
     * - Package tree
     * - Namespace tree
     * - Marker list
     * - Deprecated elements listing
     * - Removal of objects related to visibility
     *
     * @return void
     */
    public function finalize()
    {
        // filter all undesired tags
        if (count($this->parser->getIgnoredTags()) > 0) {
            $query = '//tag[@name=\''
                . implode('\']|//tag[@name=\'', $this->parser->getIgnoredTags())
                . '\']';
            $xpath = new \DOMXPath($this->xml);
            $qry = $xpath->query($query);

            /** @var \DOMElement $item */
            for ($i = 0; $i < $qry->length; $i++) {
                $qry->item($i)->parentNode->removeChild($qry->item($i));
            }
        }

        $this->buildPackageTree($this->xml);
        $this->buildNamespaceTree($this->xml);
        $this->buildMarkerList($this->xml);
        $this->buildDeprecationList($this->xml);
        $this->filterVisibility($this->xml, $this->parser->getVisibility());
    }

    /**
     * Renders the reflected file to a structure file.
     *
     * @param FileReflector $file File to export.
     *
     * @return void
     */
    public function export($file)
    {
        $object = new FileExporter();
        $object->include_source = $this->include_source;
        $object->export($this->xml->documentElement, $file);
    }

    /**
     * Collects all packages and subpackages, and adds a new section in the
     * DOM to provide an overview.
     *
     * @param \DOMDocument $dom Packages are extracted and a summary inserted
     *     in this object.
     *
     * @return void
     */
    protected function buildPackageTree(\DOMDocument $dom)
    {
        $this->log('Collecting all packages');
        $xpath = new \DOMXPath($dom);
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
     * @param \DOMDocument $dom Namespaces are extracted and a summary inserted
     *     in this object.
     *
     * @return void
     */
    protected function buildNamespaceTree(\DOMDocument $dom)
    {
        $this->log('Collecting all namespaces');
        $xpath = new \DOMXPath($dom);
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
     * @param \DOMDocument $dom Markers are extracted and a summary inserted in
     *     this object.
     *
     * @return void
     */
    protected function buildMarkerList(\DOMDocument $dom)
    {
        $this->log('Collecting all marker types');

        foreach ($this->parser->getMarkers() as $marker) {

            $marker = strtolower($marker);
            $nodes = $this->getNodeListForTagBasedQuery($dom, $marker);

            $node = new \DOMElement('marker', $marker);
            $dom->documentElement->appendChild($node);
            $node->setAttribute('count', $nodes->length);
        }
    }

    /**
     * Adds a node to the xml for deprecations and the count value
     *
     * @param \DOMDocument $dom Markers are extracted and a summary inserted in
     *     this object.
     *
     * @return void
     */
    protected function buildDeprecationList(\DOMDocument $dom)
    {
        $this->log('Counting all deprecations');

        $nodes = $this->getNodeListForTagBasedQuery($dom, 'deprecated');

        $node = new \DOMElement('deprecated');
        $dom->documentElement->appendChild($node);
        $node->setAttribute('count', $nodes->length);
    }

    /**
     * Build a tag based query string and return result
     *
     * @param \DOMDocument $dom    Markers are extracted and a summary inserted
     *      in this object.
     * @param string       $marker The marker we're searching for throughout xml
     *
     * @return \DOMNodeList
     */
    protected function getNodeListForTagBasedQuery($dom ,$marker)
    {
        $xpath = new \DOMXPath($dom);

        $query = '/project/file/markers/'.$marker.'|';
        $query .= '/project/file/docblock/tag[@name="'.$marker.'"]|';
        $query .= '/project/file/class/docblock/tag[@name="'.$marker.'"]|';
        $query .= '/project/file/class/*/docblock/tag[@name="'.$marker.'"]|';
        $query .= '/project/file/interface/docblock/tag[@name="'.$marker.'"]|';
        $query .= '/project/file/interface/*/docblock/tag[@name="'.$marker.'"]|';
        $query .= '/project/file/function/docblock/tag[@name="'.$marker.'"]|';
        $query .= '/project/file/constant/docblock/tag[@name="'.$marker.'"]';

        $nodes = $xpath->query($query);
        return $nodes;
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
            if ($namespace == '') {
                $namespace = 'global';
            }

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
     * @param array[]     $namespaces     the list of namespaces to process.
     * @param \DOMElement $parent_element the node to receive the children of
     *                                    the above list.
     * @param string      $node_name      the name of the summary element.
     *
     * @return void
     */
    protected function generateNamespaceElements($namespaces, $parent_element,
        $node_name = 'namespace'
    ) {
        foreach ($namespaces as $name => $sub_namespaces) {
            $node = new \DOMElement($node_name);
            $parent_element->appendChild($node);
            $node->setAttribute('name', $name);
            $node->setAttribute(
                'full_name',
                $parent_element->nodeName == $node_name
                ? $parent_element->getAttribute('full_name').'\\'.$name
                : $name
            );
            $this->generateNamespaceElements($sub_namespaces, $node, $node_name);
        }
    }

    /**
     * Filter the function visibility based on options used
     *
     * @param \DOMDocument $dom        Markers are extracted and a summary
     *     inserted in this object.
     * @param array        $visibility The visibility we want to filter on
     *
     * @return void
     */
    protected function filterVisibility($dom, array $visibility)
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

        $xpath = new \DOMXPath($dom);
        $nodes = $xpath->query($qry);

        /** @var \DOMElement $node */
        foreach ($nodes as $node) {
            if (($node->nodeName == 'tag')
                && ($node->parentNode->parentNode->parentNode)
            ) {
                $remove = $node->parentNode->parentNode;

                // if a parent was removed before this child we get warnings
                // that we cannot detect before hand. So we check for a nodeName
                // and if thar returns null then the node has been deleted in
                // the mean time.
                if (@$node->nodeName === null) {
                    continue;
                }
                $node->parentNode->parentNode->parentNode->removeChild($remove);
            } else {
                $node->parentNode->removeChild($node);
            }
        }
    }

    /**
     * Returns the DOMDocument for this exporter.
     *
     * @return \DOMDocument
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
