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
class DocBlox_Transformer_Behaviour_Inherit_Node_Class extends
    DocBlox_Transformer_Behaviour_Inherit_Node_Abstract
{

    /**
     * @var DOMXPath
     */
    protected $xpath = null;

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
    public function __construct(DOMElement $node, DOMXPath $xpath)
    {
        parent::__construct($node);

        $this->xpath = $xpath;
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
        if (isset($super['classes'][$parent])) {
            $docblock = $this->getDocBlockElement();

            /** @var DOMElement $super_object  */
            $super_object = $super['classes'][$parent]['object'];

            /** @var DOMElement $super_docblock  */
            $super_docblock = current(
                $this->getDirectElementsByTagName($super_object, 'docblock')
            );

            $super_class = current(
                $this->getDirectElementsByTagName($super_object, 'full_name')
            )->nodeValue;

            // add an element which defines which class' element you override
            $this->node->appendChild(new DOMElement('overrides-from', $super_class));

            $this->copyShortDescription($super_docblock, $docblock);
            $this->copyLongDescription($super_docblock, $docblock);
            $this->copyTags($this->inherited_tags, $super_docblock, $docblock);
        }

        // only add if this has a docblock; otherwise it is useless
        $docblocks = $this->getDirectElementsByTagName($this->node, 'docblock');
        if (count($docblocks) > 0) {
            $super['classes'][$node_name] = array(
                'class' => $class_name,
                'object' => $this->node
            );
        }


        /** @var DOMElement[] $method */
        $methods = $this->getDirectElementsByTagName($this->node, 'method');
        foreach ($methods as $method) {
            $inherit = new DocBlox_Transformer_Behaviour_Inherit_Node_Method($method);
            $inherit->apply($super['methods'], $class_name);
        }

        /** @var DOMElement[] $method */
        $properties = $this->getDirectElementsByTagName($this->node, 'property');
        foreach ($properties as $property) {
            $inherit = new DocBlox_Transformer_Behaviour_Inherit_Node_Property($property);
            $inherit->apply($super['properties'], $class_name);
        }

        // apply inheritance to every class or interface extending this one
        $result = $this->xpath->query(
            '/project/file/*[extends="' . $class_name . '"'
            . ' or implements="' . $class_name . '"]'
        );
        foreach ($result as $node)
        {
            $inherit = new DocBlox_Transformer_Behaviour_Inherit_Node_Class(
                $node, $this->xpath
            );
            $inherit->apply($super, $class_name);
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