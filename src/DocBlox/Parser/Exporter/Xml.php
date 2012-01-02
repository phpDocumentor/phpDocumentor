<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category  DocBlox
 * @package   Parser\Exporter
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://docblox-project.org
 */

/**
 * Class responsible for writing the results of the Reflection to a single
 * Intermediate Structure file in XML.
 *
 * @category DocBlox
 * @package  Parser\Exporter
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://docblox-project.org
 */
class DocBlox_Parser_Exporter_Xml extends DocBlox_Parser_Exporter_Abstract
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

        $document_element->setAttribute('version', DocBlox_Core_Abstract::VERSION);
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
     * @param DocBlox_Reflection_File $file File to export.
     *
     * @return void
     */
    public function export(DocBlox_Reflection_File $file)
    {
        $child = new DOMElement('file');
        $this->xml->documentElement->appendChild($child);

        $child->setAttribute('path', ltrim($file->getFilename(), './'));
        $child->setAttribute('hash', $file->getHash());

        $this->exportDocBlock($child, $file);

        // add markers
        if (count($file->getMarkers()) > 0) {
            $markers = new DOMElement('markers');
            $child->appendChild($markers);

            foreach ($file->getMarkers() as $marker) {
                $marker_obj = new DOMElement(
                    strtolower($marker[0]),
                    htmlspecialchars(trim($marker[1]))
                );
                $markers->appendChild($marker_obj);
                $marker_obj->setAttribute('line', $marker[2]);
            }
        }

        if (count($file->getParseErrors()) > 0) {
            $parse_errors = new DOMElement('parse_markers');
            $child->appendChild($parse_errors);

            foreach ($file->getParseErrors() as $error) {
                $marker_obj = new DOMElement(
                    strtolower($error[0]),
                    htmlspecialchars(trim($error[1]))
                );
                $parse_errors->appendChild($marker_obj);
                $marker_obj->setAttribute('line', $error[2]);
            }
        }

        // add namespace aliases
        foreach ($file->getNamespaceAliases() as $alias => $namespace) {
            $alias_obj = new DOMElement('namespace-alias', $namespace);
            $child->appendChild($alias_obj);
            $alias_obj->setAttribute('name', $alias);
        }

        /** @var DocBLox_Reflection_Include $include */
        foreach ($file->getIncludes() as $include) {
            $this->exportInclude($child, $include);
        }

        /** @var DocBlox_Reflection_Constant $constant */
        foreach ($file->getConstants() as $constant) {
            $constant->setDefaultPackageName($file->getDefaultPackageName());
            $this->exportConstant($child, $constant);
        }

        /** @var DocBlox_Reflection_Function $function */
        foreach ($file->getFunctions() as $function) {
            $function->setDefaultPackageName($file->getDefaultPackageName());
            $this->exportFunction($child, $function);
        }

        /** @var DocBlox_Reflection_Interface $interface */
        foreach ($file->getInterfaces() as $interface) {
            $interface->setDefaultPackageName($file->getDefaultPackageName());
            $this->exportInterface($child, $interface);
        }

        /** @var DocBlox_Reflection_Class $class */
        foreach ($file->getClasses() as $class) {
            $class->setDefaultPackageName($file->getDefaultPackageName());
            $this->exportClass($child, $class);
        }

        // if we want to include the source for each file; append a new
        // element 'source' which contains a compressed, encoded version
        // of the source
        if ($this->include_source) {
            $child->appendChild(
                new DOMElement(
                    'source',
                    base64_encode(gzcompress($file->getContents()))
                )
            );
        }
    }

    protected function exportClass(
        DOMElement $parent, DocBlox_Reflection_Class $class
    ) {
        $child = new DOMElement('class');
        $parent->appendChild($child);

        $child->setAttribute('final', $class->isFinal() ? 'true' : 'false');
        $child->setAttribute('abstract', $class->isAbstract() ? 'true' : 'false');

        $this->exportInterface($parent, $class, $child);
    }

    protected function exportInterface(
        DOMElement $parent, DocBlox_Reflection_Interface $interface, $child = null
    ) {
        if ($child === null) {
            $child = new DOMElement('interface');
            $parent->appendChild($child);
        }

        $child->setAttribute('namespace', $interface->getNamespace());
        $child->setAttribute('line', $interface->getLineNumber());

        $child->appendChild(new DOMElement('name', $interface->getName()));
        $child->appendChild(
            new DOMElement(
                'full_name', $interface->expandType($interface->getName(), true)
            )
        );
        $child->appendChild(
            new DOMElement('extends', $interface->getParentClass()
                ? $interface->expandType($interface->getParentClass(), true)
                : '')
        );

        foreach ($interface->getParentInterfaces() as $parent_interface) {
            $child->appendChild(
                new DOMElement(
                    'extends', $interface->expandType($parent_interface, true)
                )
            );
        }

        $this->exportDocBlock($child, $interface);

        foreach ($interface->getConstants() as $constant) {
            $this->exportConstant($child, $constant);
        }
        foreach ($interface->getProperties() as $property) {
            $this->exportProperty($child, $property);
        }
        foreach ($interface->getMethods() as $method) {
            $this->exportMethod($child, $method);
        }
    }

    protected function exportMethod(
        DOMElement $parent, DocBlox_Reflection_Method $method
    ) {
        $child = new DOMElement('method');
        $parent->appendChild($child);

        $child->setAttribute('final', $method->isFinal() ? 'true' : 'false');
        $child->setAttribute('abstract', $method->isAbstract() ? 'true' : 'false');
        $child->setAttribute('static', $method->isStatic() ? 'true' : 'false');
        $child->setAttribute('visibility', $method->getVisibility());
        $child->setAttribute('line', $method->getLineNumber());

        $child->appendChild(new DOMElement('name', $method->getName()));

        $this->exportDocBlock($child, $method);

        // import methods into class xml
        foreach ($method->getArguments() as $argument) {
            $this->exportArgument($child, $argument);
        }
    }

    protected function exportConstant(
        DOMElement $parent, DocBlox_Reflection_Constant $constant
    ) {
        if (!$constant->getName()) {
            return;
        }

        $child = new DOMElement('constant');
        $parent->appendChild($child);

        $child->setAttribute('namespace', $constant->getNamespace());
        $child->setAttribute('line', $constant->getLineNumber());

        $child->appendChild(new DOMElement('name', $constant->getName()));
        $child->appendChild(new DOMElement('value', $constant->getValue()));

        $this->exportDocBlock($child, $constant);
    }

    protected function exportVariable(
        DOMElement $parent, DocBlox_Reflection_Variable $variable, $child = null
    ) {
        if ($child === null) {
            $child = new DOMElement('variable');
            $parent->appendChild($child);
        }

        $child->setAttribute('line', $variable->getLineNumber());

        $child->appendChild(new DOMElement('name', $variable->getName()));
        $child->appendChild(new DOMElement('default', $variable->getDefault()));

        $this->exportDocBlock($child, $variable);
    }

    protected function exportProperty(
        DOMElement $parent, DocBlox_Reflection_Property $property)
    {
        $child = new DOMElement('property');
        $parent->appendChild($child);

        $child->setAttribute('final', $property->isFinal() ? 'true' : 'false');
        $child->setAttribute('static', $property->isStatic() ? 'true' : 'false');
        $child->setAttribute('visibility', $property->getVisibility());

        $this->exportVariable($parent, $property, $child);
    }

    protected function exportFunction(
        DOMElement $parent, DocBlox_Reflection_Function $function
    ) {
        $child = new DOMElement('function');
        $parent->appendChild($child);

        $child->setAttribute('namespace', $function->getNamespace());
        $child->setAttribute('line', $function->getLineNumber());

        $child->appendChild(new DOMElement('name', $function->getName()));
        $child->appendChild(new DOMElement('type', $function->getType()));

        $this->exportDocBlock($child, $function);

        foreach ($function->getArguments() as $argument) {
            $this->exportArgument($child, $argument);
        }
    }

    protected function exportInclude(
        DOMElement $parent, DocBlox_Reflection_Include $include
    ) {
        $child = new DOMElement('include');
        $parent->appendChild($child);

        $child->setAttribute('line', $include->getLineNumber());
        $child->setAttribute('type', $include->getType());

        $child->appendChild(new DOMElement('name', $include->getName()));
    }

    protected function exportArgument(
        DOMElement $parent, DocBlox_Reflection_Argument $argument
    ) {
        $child = new DOMElement('argument');
        $parent->appendChild($child);

        $child->setAttribute('line', $argument->getLineNumber());
        $child->appendChild(new DOMElement('name', $argument->getName()));
        $child->appendChild(new DOMElement('default', $argument->getDefault()));
        $child->appendChild(new DOMElement('type', $argument->getType()));
    }

    protected function exportDocBlock(
        DOMElement $parent, DocBlox_Reflection_DocBlockedAbstract $element
    ) {
        $docblock = $element->getDocBlock();

        if (!$docblock) {
            return;
        }

        $child = new DOMElement('docblock');
        $parent->appendChild($child);

        // TODO: custom attached member variable, make real
        $child->setAttribute('line', $docblock->line_number);

        $child->appendChild(
            new DOMElement(
                'description', htmlentities($docblock->getShortDescription())
            )
        );
        $child->appendChild(
            new DOMElement('long-description', $docblock->getLongDescription()
                ->getFormattedContents())
        );

        foreach($docblock->getTags() as $tag) {
            $this->exportDocBlockTag($child, $tag, $element);
        }

        /** @var DocBlox_Reflection_DocBlock_Tag $package */
        $package = current($docblock->getTagsByName('package'));

        /** @var DocBlox_Reflection_DocBlock_Tag $subpackage */
        $subpackage = current($docblock->getTagsByName('subpackage'));

        $package_name = '';
        if ($package) {
            $package_name = str_replace(
                array('.', '_'),
                '\\',
                $package->getContent()
                . ($subpackage ? '\\' . $subpackage->getContent() : '')
            );
        }

        if (!$package_name) {
            $package_name = $element->getDefaultPackageName();
        }

        $parent->setAttribute('package', $package_name);
    }

    protected function exportDocBlockTag(
        DOMElement $parent, DocBlox_Reflection_DocBlock_Tag $tag,
        DocBlox_Reflection_DocBlockedAbstract $element
    ) {
        $child = new DOMElement('tag');
        $parent->appendChild($child);

        $child->setAttribute('line', $parent->getAttribute('line'));

        $element->dispatch(
            'reflection.docblock.tag.export',
            array(
                'object' => $tag,
                'xml' => simplexml_import_dom($child)
            )
        );
    }

    /**
     * Adds the DocBlock XML definition to the given SimpleXMLElement.
     *
     * @param SimpleXMLElement                      $xml
     *     SimpleXMLElement to be appended to
     * @param DocBlox_Reflection_DocBlockedAbstract $parent Parent object to
     *     enhance.
     *
     * @return void
     */
    protected function addDocblockToSimpleXmlElement(
        SimpleXMLElement $xml, DocBlox_Reflection_DocBlockedAbstract $parent
    ) {
        $docblock             = $parent->getDocBlock();
        $default_package_name = $parent->getDefaultPackageName();
        $package              = '';
        $subpackage           = '';

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

                $parent->dispatch(
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
     * Returns the XML contents of this export.
     *
     * @return string
     */
    public function getContents()
    {
        return $this->xml->saveXML();
    }
}