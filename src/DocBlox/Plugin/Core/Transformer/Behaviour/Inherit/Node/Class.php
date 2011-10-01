<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category   DocBlox
 * @package    Transformer
 * @subpackage Behaviour
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */

/**
 * Responsible for adding inheritance behaviour to an individual class.
 *
 * @category   DocBlox
 * @package    Transformer
 * @subpackage Behaviour
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
class DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Class extends
    DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Abstract
{

    /**
     * @var DOMXPath
     */
    protected $document = null;

    /** @var string[] All class tags that are inherited when none are defined */
    protected $inherited_tags = array(
        'package',
        'subpackage',
        'version',
        'copyright',
        'author'
    );

    /**
     * Initializes this node and registers the XPath object.
     *
     * @param DOMElement $node
     * @param DOMXPath   $xpath
     */
    public function __construct(DOMElement $node, DOMDocument $document)
    {
        parent::__construct($node);

        $this->document = $document;
    }

    /**
     * Returns the name of the given class or interface node.
     *
     * @return string
     */
    protected function getNodeName()
    {
        return current(
            $this->getDirectElementsByTagName($this->node, 'full_name')
        )->nodeValue;
    }

    /**
     * Checks whether the super contains any reference to the existing methods
     * or properties.
     *
     * $super is not a real class, it is an aggregation of all methods and
     * properties in the inheritance tree. This is done because a method may
     * override another method which is not in the direct parent but several
     * levels upwards.
     *
     * To deal with the situation above we flatten every found $sub into
     * the $super. We only store the properties and methods since anything
     * else does not override.
     *
     * The structure of $super is:
     * * methods, array containing `$method_name => array` pairs
     *   * class, name of the deepest leaf where this method is encountered
     *   * object, DOMElement of the method declaration in the deepest leaf
     * * properties, array containing `$property_name => array` pairs
     *   * class, name of the deepest leaf where this property is encountered
     *   * object, DOMElement of the property declaration in the deepest leaf
     *
     * @param array      $super
     * @param string     $class_name Not used; required by the Abstract class
     *
     * @return void
     */
    public function apply(array &$super, $class_name)
    {
        // explicitly make a copy of the super array; every other element should
        // have the $super as reference, except class.
        // When $super is used by reference in this case then other classes will
        // be polluted with methods from sibling classes.
        $super_copy = $super;

        $class_name = current(
            $this->getDirectElementsByTagName($this->node, 'full_name')
        )->nodeValue;

        // the name is always the first encountered child element with
        // tag name 'name'
        $node_name = $this->getNodeName();
        $parent = current(
            $this->getDirectElementsByTagName($this->node, 'extends')
        )->nodeValue;

        // only process if the super has a node with this name
        if (isset($super_copy['classes'][$parent])) {
            $docblock = $this->getDocBlockElement();

            /** @var DOMElement $super_object  */
            $super_object = $super_copy['classes'][$parent]['object'];

            /** @var DOMElement $super_docblock  */
            $super_docblock = current(
                $this->getDirectElementsByTagName($super_object, 'docblock')
            );

            $super_class = current(
                $this->getDirectElementsByTagName($super_object, 'full_name')
            )->nodeValue;

            // add an element which defines which class' element you override
            $this->node->appendChild(new DOMElement('overrides-from', $super_class));

            if ($super_docblock)
            {
                $this->copyShortDescription($super_docblock, $docblock);
                $this->copyLongDescription($super_docblock, $docblock);
                $this->copyTags($this->inherited_tags, $super_docblock, $docblock);
            }
        }

        $super_copy['classes'][$node_name] = array(
            'class' => $class_name,
            'object' => $this->node
        );

        /** @var DOMElement[] $method */
        $methods = $this->getDirectElementsByTagName($this->node, 'method');
        $method_names = array();
        foreach ($methods as $method) {
            $method_names[] = $method->getElementsByTagName('name')->item(0)->nodeValue;

            // only process 'real' methods
            if ($method->getAttribute('inherited_from')) {
                continue;
            }
            $inherit = new DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Method($method);
            $inherit->apply($super_copy['methods'], $class_name);
        }

        // if a method present in the super classes but it is not declared
        // in this class then add it as an 'inherited_from' method.
        // explicitly do not updates the $super['methods'] array as this is mere
        // a virtual method and not one that counts for inheritance.
        foreach($super_copy['methods'] as $method_name => $method_collection) {
            // only copy methods that are not overridden and are not private
            if (in_array($method_name, $method_names)
                || ($method_collection['object']
                        ->getAttribute('visibility') == 'private')
            ) {
                continue;
            }

            // add an element 'inherited_from' to the method itself
            /** @var DOMElement $node */
            $node = clone $method_collection['object'];
            $this->node->appendChild($node);
            $node->appendChild(
                new DOMElement('inherited_from', $method_collection['class'])
            );

            // get the docblock or create a new one if it doesn't exist
            $docblocks = $node->getElementsByTagName('docblock');
            if ($docblocks->length == 0) {
                $docblock = new DOMElement('docblock');
                $node->appendChild($docblock);
            } else {
                $docblock = $docblocks->item(0);
            }

            // adds a new inherited_from to signify that this method is not
            // declared in this class but inherited from a base class
            $inherited_from_tag = new DOMElement('tag');
            $docblock->appendChild($inherited_from_tag);
            $inherited_from_tag->setAttribute('name', 'inherited_from');
            $inherited_from_tag->setAttribute(
                'refers',
                $method_collection['class'].'::'.$method_name.'()'
            );
            $inherited_from_tag->setAttribute(
                'description',
                $method_collection['class'].'::'.$method_name.'()'
            );
        }

        /** @var DOMElement[] $method */
        $property_names = array();
        $properties = $this->getDirectElementsByTagName($this->node, 'property');
        foreach ($properties as $property) {
            $property_names[] = $property->getElementsByTagName('name')->item(0)->nodeValue;

            // only process 'real' methods
            if ($property->getAttribute('inherited_from')) {
                continue;
            }
            $inherit = new DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Property($property);
            $inherit->apply($super_copy['properties'], $class_name);
        }

        // if a property is present in the super classes but it is not declared
        // in this class then add it as an 'inherited_from' property.
        // explicitly do not updates the $super['properties'] array as this is
        // mere a virtual property and not one that counts for inheritance.
        foreach ($super_copy['properties'] as $property_name => $property_collection) {
            // only copy methods that are not overridden and are not private
            if (in_array($property_name, $property_names)
                || ($property_collection['object']
                            ->getAttribute('visibility') == 'private')
            ) {
                continue;
            }

            // add an element 'inherited_from' to the method itself
            /** @var DOMElement $node */
            $node = clone $property_collection['object'];
            $this->node->appendChild($node);
            $node->appendChild(
                new DOMElement('inherited_from', $property_collection['class'])
            );

            // get the docblock or create a new one if it doesn't exist
            $docblocks = $node->getElementsByTagName('docblock');
            if ($docblocks->length == 0) {
                $docblock = new DOMElement('docblock');
                $node->appendChild($docblock);
            } else {
                $docblock = $docblocks->item(0);
            }

            // adds a new inherited_from to signify that this method is not
            // declared in this class but inherited from a base class
            $inherited_from_tag = new DOMElement('tag');
            $docblock->appendChild($inherited_from_tag);
            $inherited_from_tag->setAttribute('name', 'inherited_from');
            $inherited_from_tag->setAttribute(
                'refers',
                $property_collection['class'] . '::' . $property_name
            );
            $inherited_from_tag->setAttribute(
                'description',
                $property_collection['class'] . '::' . $property_name
            );
        }

        // apply inheritance to every class or interface extending this one
        $xpath = new DOMXPath($this->document);

        $xpath_class_name = 'concat(\''.str_replace(
            array("'", '"'),
            array('\', "\'", \'', '\', \'"\' , \''),
            $class_name
        ) . "', '')";

        $qry = '/project/file/class[extends=' . $xpath_class_name
            . ' or implements=' . $xpath_class_name . ']'
            . '|/project/file/interface[extends=' . $xpath_class_name . ']';

        $result = @$xpath->query($qry);
        if ($result === false) {
            var_dump($xpath_class_name);
            throw new DocBlox_Plugin_Core_Exception(
                'Invalid xpath query in Class inheritance: '. $qry
            );
        }

        foreach ($result as $node)
        {
            $child_class_name = $node->getElementsByTagName('full_name')
                ->item(0)->nodeValue;

            if (!$child_class_name)
            {
                throw new Exception(
                    'A class was encountered with no FQCN. This should not ' .
                    'happen; please contact the DocBlox developers to have them '
                    . 'analyze this issue'
                );
            }

            $inherit = new DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Class(
                $node, $this->document
            );
            $inherit->apply($super_copy, $class_name);
        }
    }

    /**
     * Override the parent's copyTags method to check whether the package names
     * match; if not: do not copy the subpackage.
     *
     * Frameworks often extend classes from other frameworks; and applications
     * extend classes of frameworks.
     *
     * Without this check when the framework specifies a subpackage but the
     * extending class would not; and the packages would not match. Then a
     * subpackage would be applied that is not applicable to this item.
     *
     * Additionally; this package/subpackage combination would not be present in
     * the package index int he structure file and the classes would never be
     * shown in the navigation.
     *
     * @param string[]   $tag_types      List of allowed tag types.
     * @param DOMElement $super_docblock Super class' docblock.
     * @param DOMElement $docblock       Sub class' docblock.
     *
     * @return void
     */
    protected function copyTags(array $tag_types, DOMElement $super_docblock,
        DOMElement $docblock)
    {
        // find the name of the super's package
        $super_package_name = null;
        $tags = $this->getDirectElementsByTagName($super_docblock, 'tag');
        foreach($tags as $tag) {
            if ($tag->getAttribute('name') == 'package') {
                $super_package_name = $tag->getAttribute('description');
                break;
            }
        }

        // find the name of the local's package
        $local_package_name = null;
        $tags = $this->getDirectElementsByTagName($docblock, 'tag');
        foreach ($tags as $tag) {
            if ($tag->getAttribute('name') == 'package') {
                $local_package_name = $tag->getAttribute('description');
                break;
            }
        }

        // if the package names do not match; do not inherit the subpackage
        if ($super_package_name != $local_package_name) {
            foreach($tag_types as $key => $type) {
                if ($type == 'subpackage') {
                    unset($tag_types[$key]);
                }
            }
        }

        parent::copyTags($tag_types, $super_docblock, $docblock);
    }


}