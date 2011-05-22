<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category   DocBlox
 * @package    Transformation
 * @subpackage Behaviour.Inherit
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */

/**
 * Responsible for adding inheritance behaviour to an individual class.
 *
 * @category   DocBlox
 * @package    Transformation
 * @subpackage Behaviour.Inherit
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

}