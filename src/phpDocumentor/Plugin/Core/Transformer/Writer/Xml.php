<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Core\Transformer\Writer;

use phpDocumentor\Descriptor\Validator\Error;
use phpDocumentor\Plugin\Core\Transformer\Writer\Xml\ArgumentConverter;
use phpDocumentor\Plugin\Core\Transformer\Writer\Xml\ConstantConverter;
use phpDocumentor\Plugin\Core\Transformer\Writer\Xml\DocBlockConverter;
use phpDocumentor\Plugin\Core\Transformer\Writer\Xml\InterfaceConverter;
use phpDocumentor\Plugin\Core\Transformer\Writer\Xml\MethodConverter;
use phpDocumentor\Plugin\Core\Transformer\Writer\Xml\PropertyConverter;
use phpDocumentor\Plugin\Core\Transformer\Writer\Xml\TagConverter;
use phpDocumentor\Plugin\Core\Transformer\Writer\Xml\TraitConverter;
use phpDocumentor\Transformer\Router\RouterAbstract;
use phpDocumentor\Transformer\Writer\WriterAbstract;
use phpDocumentor\Transformer\Writer\Translatable;
use phpDocumentor\Application;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\FunctionDescriptor;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\TraitDescriptor;
use phpDocumentor\Plugin\Core\Transformer\Behaviour\Tag\AuthorTag;
use phpDocumentor\Plugin\Core\Transformer\Behaviour\Tag\CoversTag;
use phpDocumentor\Plugin\Core\Transformer\Behaviour\Tag\IgnoreTag;
use phpDocumentor\Plugin\Core\Transformer\Behaviour\Tag\InternalTag;
use phpDocumentor\Plugin\Core\Transformer\Behaviour\Tag\LicenseTag;
use phpDocumentor\Plugin\Core\Transformer\Behaviour\Tag\MethodTag;
use phpDocumentor\Plugin\Core\Transformer\Behaviour\Tag\ParamTag;
use phpDocumentor\Plugin\Core\Transformer\Behaviour\Tag\PropertyTag;
use phpDocumentor\Plugin\Core\Transformer\Behaviour\Tag\ReturnTag;
use phpDocumentor\Plugin\Core\Transformer\Behaviour\Tag\UsesTag;
use phpDocumentor\Plugin\Core\Transformer\Behaviour\Tag\VarTag;
use phpDocumentor\Transformer\Transformation;
use phpDocumentor\Transformer\Transformer;
use phpDocumentor\Translator\Translator;

/**
 * Converts the structural information of phpDocumentor into an XML file.
 */
class Xml extends WriterAbstract implements Translatable
{
    /** @var \DOMDocument $xml */
    protected $xml;

    /** @var Translator $translator */
    protected $translator;

    protected $docBlockConverter;

    protected $argumentConverter;

    protected $methodConverter;

    protected $propertyConverter;

    protected $constantConverter;

    protected $interfaceConverter;

    protected $traitConverter;

    public function __construct(RouterAbstract $router)
    {
        $this->docBlockConverter  = new DocBlockConverter(new TagConverter(), $router);
        $this->argumentConverter  = new ArgumentConverter();
        $this->methodConverter    = new MethodConverter($this->argumentConverter, $this->docBlockConverter);
        $this->propertyConverter  = new PropertyConverter($this->docBlockConverter);
        $this->constantConverter  = new ConstantConverter($this->docBlockConverter);
        $this->interfaceConverter = new InterfaceConverter(
            $this->docBlockConverter,
            $this->methodConverter,
            $this->constantConverter
        );
        $this->traitConverter = new TraitConverter(
            $this->docBlockConverter,
            $this->methodConverter,
            $this->propertyConverter
        );
    }

    /**
     * Returns an instance of the object responsible for translating content.
     *
     * @return Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * Sets a new object capable of translating strings on this writer.
     *
     * @param Translator $translator
     *
     * @return void
     */
    public function setTranslator(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * This method generates the AST output
     *
     * @param ProjectDescriptor $project        Document containing the structure.
     * @param Transformation    $transformation Transformation to execute.
     *
     * @return void
     */
    public function transform(ProjectDescriptor $project, Transformation $transformation)
    {
        $artifact = $this->getDestinationPath($transformation);

        $this->checkForSpacesInPath($artifact);

        $this->xml = new \DOMDocument('1.0', 'utf-8');
        $this->xml->formatOutput = true;
        $document_element = new \DOMElement('project');
        $this->xml->appendChild($document_element);

        $document_element->setAttribute('title', $project->getName());
        $document_element->setAttribute('version', Application::$VERSION);

        $this->buildPartials($document_element, $project);

        $transformer = $transformation->getTransformer();

        foreach ($project->getFiles() as $file) {
            $this->buildFile($document_element, $file, $transformer);
        }

        $this->finalize($project);
        file_put_contents($artifact, $this->xml->saveXML());
    }

    protected function buildPartials(\DOMElement $parent, ProjectDescriptor $project)
    {
        $child = new \DOMElement('partials');
        $parent->appendChild($child);
        foreach ($project->getPartials() as $name => $element) {
            $partial = new \DOMElement('partial');
            $child->appendChild($partial);
            $partial->setAttribute('name', $name);
            $partial->appendChild(new \DOMText($element));
        }
    }

    protected function buildFile(\DOMElement $parent, FileDescriptor $file, Transformer $transformer)
    {
        $child = new \DOMElement('file');
        $parent->appendChild($child);

        $path = ltrim($file->getPath(), './');
        $child->setAttribute('path', $path);
        $child->setAttribute(
            'generated-path',
            $transformer->generateFilename($path)
        );
        $child->setAttribute('hash', $file->getHash());

        $this->docBlockConverter->convert($child, $file);

        // add namespace aliases
        foreach ($file->getNamespaceAliases() as $alias => $namespace) {
            $alias_obj = new \DOMElement('namespace-alias', $namespace);
            $child->appendChild($alias_obj);
            $alias_obj->setAttribute('name', $alias);
        }

        /** @var ConstantDescriptor $constant */
        foreach ($file->getConstants() as $constant) {
            $this->constantConverter->convert($child, $constant);
        }

        /** @var FunctionDescriptor $function */
        foreach ($file->getFunctions() as $function) {
            $this->buildFunction($child, $function);
        }

        /** @var InterfaceDescriptor $interface */
        foreach ($file->getInterfaces() as $interface) {
            $this->interfaceConverter->convert($child, $interface);
        }

        /** @var ClassDescriptor $class */
        foreach ($file->getClasses() as $class) {
            $this->buildClass($child, $class);
        }

        /** @var TraitDescriptor $class */
        foreach ($file->getTraits() as $trait) {
            $this->traitConverter->convert($child, $trait);
        }

        // add markers
        if (count($file->getMarkers()) > 0) {
            $markers = new \DOMElement('markers');
            $child->appendChild($markers);

            foreach ($file->getMarkers() as $marker) {
                if (! $marker['type']) {
                    continue;
                }

                $marker_obj = new \DOMElement(strtolower($marker['type']));
                $markers->appendChild($marker_obj);

                $marker_obj->appendChild(new \DOMText(trim($marker['message'])));
                $marker_obj->setAttribute('line', $marker['line']);
            }
        }

        $errors = $file->getAllErrors();
        if (count($errors) > 0) {
            $parse_errors = new \DOMElement('parse_markers');
            $child->appendChild($parse_errors);

            /** @var Error $error */
            foreach ($errors as $error) {
                $this->createErrorEntry($error, $parse_errors);
            }
        }

        // if we want to include the source for each file; append a new
        // element 'source' which contains a compressed, encoded version
        // of the source
        if ($file->getSource()) {
            $child->appendChild(new \DOMElement('source', base64_encode(gzcompress($file->getSource()))));
        }
    }

    /**
     * Creates an entry in the ParseErrors collection of a file for a given error.
     *
     * @param Error       $error
     * @param \DOMElement $parse_errors
     *
     * @return void
     */
    protected function createErrorEntry($error, $parse_errors)
    {
        $marker_obj = new \DOMElement(strtolower($error->getSeverity()));
        $parse_errors->appendChild($marker_obj);

        $message = ($this->getTranslator())
            ? vsprintf($this->getTranslator()->translate($error->getCode()), $error->getContext())
            : $error->getCode();

        $marker_obj->appendChild(new \DOMText($message));
        $marker_obj->setAttribute('line', $error->getLine());
        $marker_obj->setAttribute('code', $error->getCode());
    }

    /**
     * Retrieves the destination location for this artifact.
     *
     * @param Transformation $transformation
     *
     * @return string
     */
    protected function getDestinationPath(Transformation $transformation)
    {
        return $transformation->getTransformer()->getTarget()
            . DIRECTORY_SEPARATOR . $transformation->getArtifact();
    }

    /**
     * Export this function definition to the given parent DOMElement.
     *
     * @param \DOMElement        $parent   Element to augment.
     * @param FunctionDescriptor $function Element to export.
     * @param \DOMElement        $child    if supplied this element will be augmented instead of freshly added.
     *
     * @return void
     */
    public function buildFunction(\DOMElement $parent, FunctionDescriptor $function, \DOMElement $child = null)
    {
        if (!$child) {
            $child = new \DOMElement('function');
            $parent->appendChild($child);
        }

        $namespace = $function->getNamespace()
            ? $function->getNamespace()
            : $parent->getAttribute('namespace');
        $child->setAttribute('namespace', ltrim($namespace, '\\'));
        $child->setAttribute('line', $function->getLine());

        $child->appendChild(new \DOMElement('name', $function->getName()));
        $child->appendChild(new \DOMElement('full_name', $function->getFullyQualifiedStructuralElementName()));

        $this->docBlockConverter->convert($child, $function);

        foreach ($function->getArguments() as $argument) {
            $this->argumentConverter->convert($child, $argument);
        }
    }

    /**
     * Exports the given reflection object to the parent XML element.
     *
     * This method creates a new child element on the given parent XML element
     * and takes the properties of the Reflection argument and sets the
     * elements and attributes on the child.
     *
     * If a child DOMElement is provided then the properties and attributes are
     * set on this but the child element is not appended onto the parent. This
     * is the responsibility of the invoker. Essentially this means that the
     * $parent argument is ignored in this case.
     *
     * @param \DOMElement     $parent The parent element to augment.
     * @param ClassDescriptor $class  The data source.
     * @param \DOMElement     $child  Optional: child element to use instead of creating a
     *      new one on the $parent.
     *
     * @return void
     */
    public function buildClass(\DOMElement $parent, ClassDescriptor $class, \DOMElement $child = null)
    {
        if (!$child) {
            $child = new \DOMElement('class');
            $parent->appendChild($child);
        }

        $child->setAttribute('final', $class->isFinal() ? 'true' : 'false');
        $child->setAttribute('abstract', $class->isAbstract() ? 'true' : 'false');

        $parentFqcn = is_string($class->getParent())
            ? $class->getParent()
            : $class->getParent()->getFullyQualifiedStructuralElementName();
        $child->appendChild(new \DOMElement('extends', $parentFqcn));

        /** @var InterfaceDescriptor $interface */
        foreach ($class->getInterfaces() as $interface) {
            $interfaceFqcn = is_string($interface)
                ? $interface
                : $interface->getFullyQualifiedStructuralElementName();
            $child->appendChild(new \DOMElement('implements', $interfaceFqcn));
        }

        if ($child === null) {
            $child = new \DOMElement('interface');
            $parent->appendChild($child);
        }

        $namespace = $class->getNamespace()->getFullyQualifiedStructuralElementName();
        $child->setAttribute('namespace', ltrim($namespace, '\\'));
        $child->setAttribute('line', $class->getLine());

        $child->appendChild(new \DOMElement('name', $class->getName()));
        $child->appendChild(new \DOMElement('full_name', $class->getFullyQualifiedStructuralElementName()));

        $this->docBlockConverter->convert($child, $class);

        foreach ($class->getConstants() as $constant) {
            // TODO #840: Workaround; for some reason there are NULLs in the constants array.
            if ($constant) {
                $this->constantConverter->convert($child, $constant);
            }
        }

        foreach ($class->getInheritedConstants() as $constant) {
            // TODO #840: Workaround; for some reason there are NULLs in the constants array.
            if ($constant) {
                $this->constantConverter->convert($child, $constant);
            }
        }

        foreach ($class->getProperties() as $property) {
            // TODO #840: Workaround; for some reason there are NULLs in the properties array.
            if ($property) {
                $this->propertyConverter->convert($child, $property);
            }
        }

        foreach ($class->getInheritedProperties() as $property) {
            // TODO #840: Workaround; for some reason there are NULLs in the properties array.
            if ($property) {
                $this->propertyConverter->convert($child, $property);
            }
        }

        foreach ($class->getMethods() as $method) {
            // TODO #840: Workaround; for some reason there are NULLs in the methods array.
            if ($method) {
                $this->methodConverter->convert($child, $method);
            }
        }

        foreach ($class->getInheritedMethods() as $method) {
            // TODO #840: Workaround; for some reason there are NULLs in the methods array.
            if ($method) {
                $methodElement = $this->methodConverter->convert($child, $method);
                $methodElement->appendChild(
                    new \DOMElement(
                        'inherited_from',
                        $method->getParent()->getFullyQualifiedStructuralElementName()
                    )
                );
            }
        }
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
     * @param ProjectDescriptor $projectDescriptor
     *
     * @return void
     */
    protected function finalize(ProjectDescriptor $projectDescriptor)
    {
        // TODO: move all these behaviours to a central location for all template parsers
        $behaviour = new AuthorTag();
        $behaviour->process($this->xml);
        $behaviour = new CoversTag();
        $behaviour->process($this->xml);
        $behaviour = new IgnoreTag();
        $behaviour->process($this->xml);
        $behaviour = new InternalTag(
            $projectDescriptor->isVisibilityAllowed(ProjectDescriptor\Settings::VISIBILITY_INTERNAL)
        );
        $behaviour->process($this->xml);
        $behaviour = new LicenseTag();
        $behaviour->process($this->xml);
        $behaviour = new MethodTag();
        $behaviour->process($this->xml);
        $behaviour = new ParamTag();
        $behaviour->process($this->xml);
        $behaviour = new PropertyTag();
        $behaviour->process($this->xml);
        $behaviour = new ReturnTag();
        $behaviour->process($this->xml);
        $behaviour = new UsesTag();
        $behaviour->process($this->xml);
        $behaviour = new VarTag();
        $behaviour->process($this->xml);
        $this->buildPackageTree($this->xml);
        $this->buildNamespaceTree($this->xml);
        $this->buildDeprecationList($this->xml);
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
        $xpath = new \DOMXPath($dom);
        $packages = array('global' => true);
        $qry = $xpath->query('//@package');
        for ($i = 0; $i < $qry->length; $i++) {
            if (isset($packages[$qry->item($i)->nodeValue])) {
                continue;
            }

            $packages[$qry->item($i)->nodeValue] = true;
        }

        $packages = $this->generateNamespaceTree(array_keys($packages));
        $this->generateNamespaceElements($packages, $dom->documentElement, 'package');
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
     * Adds a node to the xml for deprecations and the count value
     *
     * @param \DOMDocument $dom Markers are extracted and a summary inserted in this object.
     *
     * @return void
     */
    protected function buildDeprecationList(\DOMDocument $dom)
    {
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
    protected function getNodeListForTagBasedQuery($dom, $marker)
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
            if (!$namespace) {
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
    protected function generateNamespaceElements($namespaces, $parent_element, $node_name = 'namespace')
    {
        foreach ($namespaces as $name => $sub_namespaces) {
            $node = new \DOMElement($node_name);
            $parent_element->appendChild($node);
            $node->setAttribute('name', $name);
            $fullName = $parent_element->nodeName == $node_name
                ? $parent_element->getAttribute('full_name') . '\\' . $name
                : $name;
            $node->setAttribute('full_name', $fullName);
            $this->generateNamespaceElements($sub_namespaces, $node, $node_name);
        }
    }
}
