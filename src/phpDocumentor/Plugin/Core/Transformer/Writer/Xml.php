<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Core\Transformer\Writer;

use phpDocumentor\Descriptor\Validator\Error;
use phpDocumentor\Transformer\Writer\WriterAbstract;
use phpDocumentor\Transformer\Writer\Translatable;
use phpDocumentor\Application;
use phpDocumentor\Descriptor\ArgumentDescriptor;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\FunctionDescriptor;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\PropertyDescriptor;
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
use phpDocumentor\Reflection\BaseReflector;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Transformer\Transformation;
use phpDocumentor\Transformer\Transformer;
use phpDocumentor\Translator;

/**
 * Converts the structural information of phpDocumentor into an XML file.
 *
 * @todo this class currently only contains the old AST format for phpDocumentor; refactor!
 * @todo merge checkstyle writer into this one with query checkstyle
 * @todo the packages may not be propagated correctly, check it
 */
class Xml extends WriterAbstract implements Translatable
{
    /** @var \DOMDocument $xml */
    protected $xml;

    /** @var Translator $translator */
    protected $translator;

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

    protected function buildFile(
        \DOMElement $parent,
        FileDescriptor $file,
        Transformer $transformer
    ) {
        $child = new \DOMElement('file');
        $parent->appendChild($child);

        $path = ltrim($file->getPath(), './');
        $child->setAttribute('path', $path);
        $child->setAttribute(
            'generated-path',
            $transformer->generateFilename($path)
        );
        $child->setAttribute('hash', $file->getHash());

        $this->buildDocBlock($child, $file);

        // add namespace aliases
        foreach ($file->getNamespaceAliases() as $alias => $namespace) {
            $alias_obj = new \DOMElement('namespace-alias', $namespace);
            $child->appendChild($alias_obj);
            $alias_obj->setAttribute('name', $alias);
        }

        /** @var ConstantDescriptor $constant */
        foreach ($file->getConstants() as $constant) {
            $this->buildConstant($child, $constant);
        }

        /** @var FunctionDescriptor $function */
        foreach ($file->getFunctions() as $function) {
            $this->buildFunction($child, $function);
        }

        /** @var InterfaceDescriptor $interface */
        foreach ($file->getInterfaces() as $interface) {
            $this->buildInterface($child, $interface);
        }

        /** @var ClassDescriptor $class */
        foreach ($file->getClasses() as $class) {
            $this->buildClass($child, $class);
        }

        // add markers
        if (count($file->getMarkers()) > 0) {
            $markers = new \DOMElement('markers');
            $child->appendChild($markers);

            foreach ($file->getMarkers() as $marker) {
                $marker_obj = new \DOMElement(strtolower($marker['type']));
                $markers->appendChild($marker_obj);

                $marker_obj->appendChild(new \DOMText(trim($marker['message'])));
                $marker_obj->setAttribute('line', $marker['line']);
            }
        }

        if (count($file->getErrors()) > 0) {
            $parse_errors = new \DOMElement('parse_markers');
            $child->appendChild($parse_errors);

            /** @var Error $error */
            foreach ($file->getAllErrors() as $error) {
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
     * Exports the given constant to the parent XML element.
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
     * @param \DOMElement        $parent   The parent element to augment.
     * @param ConstantDescriptor $constant The data source.
     * @param \DOMElement        $child    Optional: child element to use instead of creating a new one on the $parent.
     *
     * @return void
     */
    public function buildConstant(\DOMElement $parent, ConstantDescriptor $constant, \DOMElement $child = null)
    {
        if (!$constant->getName()) {
            return;
        }

        if (!$child) {
            $child = new \DOMElement('constant');
            $parent->appendChild($child);
        }

        $namespace = $constant->getNamespace()
            ? $constant->getNamespace()
            : $parent->getAttribute('namespace');
        $child->setAttribute('namespace', ltrim($namespace, '\\'));
        $child->setAttribute('line', $constant->getLine());

        $child->appendChild(new \DOMElement('name', $constant->getName()));
        $child->appendChild(new \DOMElement('full_name', $constant->getFullyQualifiedStructuralElementName()));

        $child->appendChild(new \DOMElement('value'))->appendChild(new \DOMText($constant->getValue()));

        $this->buildDocBlock($child, $constant);
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

        $this->buildDocBlock($child, $function);

        foreach ($function->getArguments() as $argument) {
            $this->buildArgument($child, $argument);
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
     * @param \DOMElement        $parent   The parent element to augment.
     * @param ArgumentDescriptor $argument The data source.
     * @param \DOMElement        $child    Optional: child element to use instead of creating a new one on the $parent.
     *
     * @return void
     */
    public function buildArgument(\DOMElement $parent, ArgumentDescriptor $argument, \DOMElement $child = null)
    {
        if (!$child) {
            $child = new \DOMElement('argument');
            $parent->appendChild($child);
        }

        $child->setAttribute('line', $argument->getLine());
        $child->setAttribute('by_reference', var_export($argument->isByReference(), true));
        $child->appendChild(new \DOMElement('name', $argument->getName()));
        $child->appendChild(new \DOMElement('default'))
            ->appendChild(new \DOMText($argument->getDefault()));

        $types = $argument->getTypes();
        $child->appendChild(new \DOMElement('type', implode('|', $types)));
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

        $this->buildDocBlock($child, $class);

        foreach ($class->getConstants() as $constant) {
            // TODO #840: Workaround; for some reason there are NULLs in the constants array.
            if ($constant) {
                $this->buildConstant($child, $constant);
            }
        }

        foreach ($class->getProperties() as $property) {
            // TODO #840: Workaround; for some reason there are NULLs in the properties array.
            if ($property) {
                $this->buildProperty($child, $property);
            }
        }

        foreach ($class->getMethods() as $method) {
            // TODO #840: Workaround; for some reason there are NULLs in the methods array.
            if ($method) {
                $this->buildMethod($child, $method);
            }
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
     * @param TraitDescriptor $trait  The data source.
     * @param \DOMElement     $child  Optional: child element to use instead of creating a
     *      new one on the $parent.
     *
     * @return void
     */
    public function buildTrait(\DOMElement $parent, TraitDescriptor $trait, \DOMElement $child = null)
    {
        if (!$child) {
            $child = new \DOMElement('trait');
            $parent->appendChild($child);
        }

        $child->setAttribute('final', $trait->isFinal() ? 'true' : 'false');
        $child->setAttribute('abstract', $trait->isAbstract() ? 'true' : 'false');

        $namespace = $trait->getNamespace();
        $child->setAttribute('namespace', ltrim($namespace, '\\'));
        $child->setAttribute('line', $trait->getLine());

        $child->appendChild(new \DOMElement('name', $trait->getName()));
        $child->appendChild(new \DOMElement('full_name', $trait->getFullyQualifiedStructuralElementName()));

        $this->buildDocBlock($child, $trait);

        foreach ($trait->getProperties() as $property) {
            $this->buildProperty($child, $property);
        }

        foreach ($trait->getMethods() as $method) {
            $this->buildMethod($child, $method);
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
     * @param \DOMElement                         $parent The parent element to augment.
     * @param InterfaceDescriptor $interface  The data source.
     * @param \DOMElement                         $child  Optional: child element to use instead of creating a
     *      new one on the $parent.
     *
     * @return void
     */
    public function buildInterface(\DOMElement $parent, InterfaceDescriptor $interface, \DOMElement $child = null)
    {
        if (!$child) {
            $child = new \DOMElement('interface');
            $parent->appendChild($child);
        }

        /** @var InterfaceDescriptor $parentInterface */
        foreach ($interface->getParent() as $parentInterface) {
            $parentFqcn = is_string($parentInterface) === false
                ? $parentInterface->getFullyQualifiedStructuralElementName()
                : $parentInterface;
            $child->appendChild(new \DOMElement('extends', $parentFqcn));
        }

        $namespace = $interface->getNamespace()->getFullyQualifiedStructuralElementName();
        $child->setAttribute('namespace', ltrim($namespace, '\\'));
        $child->setAttribute('line', $interface->getLine());

        $child->appendChild(new \DOMElement('name', $interface->getName()));
        $child->appendChild(new \DOMElement('full_name', $interface->getFullyQualifiedStructuralElementName()));

        $this->buildDocBlock($child, $interface);

        foreach ($interface->getConstants() as $constant) {
            $this->buildConstant($child, $constant);
        }

        foreach ($interface->getMethods() as $method) {
            $this->buildMethod($child, $method);
        }
    }

    /**
     * Export the given property definition to the provided parent element.
     *
     * @param \DOMElement        $parent   Element to augment.
     * @param PropertyDescriptor $property Element to export.
     *
     * @return void
     */
    public function buildProperty(\DOMElement $parent, PropertyDescriptor $property)
    {
        $child = new \DOMElement('property');
        $parent->appendChild($child);

        $child->setAttribute('static', $property->isStatic() ? 'true' : 'false');
        $child->setAttribute('visibility', $property->getVisibility());

        $child->setAttribute('line', $property->getLine());

        $namespaceFqnn = $property->getNamespace()
            ? $property->getNamespace()->getFullyQualifiedStructuralElementName()
            : $parent->getAttribute('namespace');
        $child->setAttribute('namespace', $namespaceFqnn);

        $child->appendChild(new \DOMElement('name', '$' . $property->getName()));
        $child->appendChild(new \DOMElement('default'))
            ->appendChild(new \DOMText($property->getDefault()));

        $this->buildDocBlock($child, $property);
    }

    /**
     * Export the given reflected method definition to the provided parent element.
     *
     * @param \DOMElement     $parent Element to augment.
     * @param MethodDescriptor $method Element to export.
     *
     * @return \DOMElement
     */
    public function buildMethod(\DOMElement $parent, MethodDescriptor $method)
    {
        $child = new \DOMElement('method');
        $parent->appendChild($child);

        $child->setAttribute('final', $method->isFinal() ? 'true' : 'false');
        $child->setAttribute('abstract', $method->isAbstract() ? 'true' : 'false');
        $child->setAttribute('static', $method->isStatic() ? 'true' : 'false');
        $child->setAttribute('visibility', $method->getVisibility());

        $namespaceFqnn = $method->getNamespace()
            ? $method->getNamespace()->getFullyQualifiedStructuralElementName()
            : $parent->getAttribute('namespace');
        $child->setAttribute('namespace', $namespaceFqnn);
        $child->setAttribute('line', $method->getLine());

        $child->appendChild(new \DOMElement('name', $method->getName()));
        $child->appendChild(new \DOMElement('full_name', $method->getFullyQualifiedStructuralElementName()));

        $this->buildDocBlock($child, $method);

        foreach ($method->getArguments() as $argument) {
            $this->buildArgument($child, $argument);
        }

        return $child;
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
     * @param \DOMElement        $parent  The parent element to augment.
     * @param DescriptorAbstract $element The data source.
     *
     * @return void
     */
    public function buildDocBlock(\DOMElement $parent, DescriptorAbstract $element)
    {
        $child = new \DOMElement('docblock');
        $parent->appendChild($child);

        $child->setAttribute('line', $element->getLine());
        $parent->setAttribute(
            'package',
            str_replace('&', '&amp;', ltrim($element->getPackage(), '\\'))
        );

        $this->addDescription($child, $element);
        $this->addLongDescription($child, $element);
        $this->addTags($child, $element);
    }

    /**
     * Export this tag to the given DocBlock.
     *
     * This method also invokes the 'reflection.docblock.tag.export' which can
     * be used to augment the data. This is useful for plugins so that they
     * can provide custom tags.
     *
     * @param \DOMElement   $parent  Element to augment.
     * @param Tag           $tag     The tag to export.
     * @param BaseReflector $element Element to log from.
     *
     * @return void
     */
    public function buildDocBlockTag(\DOMElement $parent, $tag, $element)
    {
        if (!$tag) {
            // TODO #840: Workaround; for some reason there are NULLs in the tags array.
            return;
        }
        $child = new \DOMElement('tag');
        $parent->appendChild($child);

        $child->setAttribute(
            'name',
            str_replace('&', '&amp;', $tag->getName())
        );
        $child->setAttribute('line', $parent->getAttribute('line'));

        $description = '';
        //@version, @deprecated, @since
        if (method_exists($tag, 'getVersion')) {
            $description .= $tag->getVersion() . ' ';
        }
        //TODO: Other previously unsupported tags are to be "serialized" here.
        $description .= $tag->getDescription();

        $child->setAttribute(
            'description',
            str_replace('&', '&amp;', trim($description))
        );

        if (method_exists($tag, 'getTypes')) {
            $typeString = '';
            foreach ($tag->getTypes() as $type) {
                $typeString .= $type . '|';
                $typeNode = $child->appendChild(new \DOMElement('type'));
                $typeNode->appendChild(new \DOMText($type));
                $lastSlashPos = strrpos($type, '\\');
                if (false !== $lastSlashPos) {
                    $typeNode->setAttribute(
                        'link',
                        str_replace('&', '&amp;', substr($type, $lastSlashPos + 1)) . '.html'
                    );
                }
            }
            $child->setAttribute(
                'type',
                str_replace('&', '&amp;', rtrim($typeString, '|'))
            );
        }
        if (method_exists($tag, 'getVariableName')) {
            $child->setAttribute(
                'variable',
                str_replace('&', '&amp;', $tag->getVariableName())
            );
        }
        if (method_exists($tag, 'getReference')) {
            $child->setAttribute(
                'link',
                str_replace('&', '&amp;', $tag->getReference())
            );
        }
        if (method_exists($tag, 'getLink')) {
            $child->setAttribute(
                'link',
                str_replace('&', '&amp;', $tag->getLink())
            );
        }
        if (method_exists($tag, 'getMethodName')) {
            $child->setAttribute(
                'method_name',
                str_replace('&', '&amp;', $tag->getMethodName())
            );
        }
    }

    /**
     * Adds the short description of $docblock to the given node as description
     * field.
     *
     * @param \DOMElement        $node
     * @param DescriptorAbstract $element
     *
     * @return void
     */
    protected function addDescription(\DOMElement $node, DescriptorAbstract $element)
    {
        $node->appendChild(new \DOMElement('description'))
            ->appendChild(new \DOMText($element->getSummary()));
    }

    /**
     * Adds the DocBlock's long description to the $child element,
     *
     * @param \DOMElement        $child
     * @param DescriptorAbstract $element
     *
     * @return void
     */
    protected function addLongDescription(\DOMElement $child, DescriptorAbstract $element)
    {
        $child
            ->appendChild(new \DOMElement('long-description'))
            ->appendChild(new \DOMText($element->getDescription()));
    }

    /**
     * Adds each tag to the $xml_node.
     *
     * @param \DOMElement        $xml_node
     * @param DescriptorAbstract $element
     *
     * @return void
     */
    protected function addTags(\DOMElement $xml_node, $element)
    {
        foreach ($element->getTags() as $tagGroup) {
            if ($tagGroup === null) {
                continue;
            }

            foreach ($tagGroup as $tag) {
                $this->buildDocBlockTag($xml_node, $tag, $element);
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
        $packages = array();
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
