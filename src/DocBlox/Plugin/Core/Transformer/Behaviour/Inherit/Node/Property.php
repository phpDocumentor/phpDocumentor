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
 * Responsible for adding inheritance behaviour to an individual property.
 *
 * @category   DocBlox
 * @package    Transformer
 * @subpackage Behaviour
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
class DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Property
    extends DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Abstract
{
    /** @var string[] Defined which tags to inherit */
    protected $inherited_tags = array('var');

    /** @var DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Class */
    protected $class = null;

    /**
     * Load the node belonging to this object, make an associative array of
     * classes and interfaces available and define the parent class.
     *
     * @param DOMElement                                                   $node
     * @param array                                                        $nodes
     * @param DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Class $class
     */
    public function __construct(
        DOMElement $node, array &$nodes,
        DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Class $class
    ) {
        parent::__construct($node, $nodes);

        $this->class = $class;
    }

    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName()
    {
        return current(
            $this->getDirectElementsByTagName($this->node, 'name')
        )->nodeValue;
    }

    /**
     * Copies this object onto the give class or interface.
     *
     * @param DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Class
     *     $class_or_interface
     *
     * @return void
     */
    function copyTo(
        DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Class
        $class_or_interface
    ) {
        // self returns this specific class and not the class of the current
        // object; thus we use get_class to retrieve it.
        $class = get_class($this);
        $inherited = new $class(
            clone $this->getNode(), $this->nodes, $class_or_interface
        );

        $class_or_interface->getNode()->appendChild($inherited->getNode());

        $inherited->getNode()->appendChild(
            new DOMElement('inherited_from', $this->class->getFQCN())
        );

        // store the origin of this element
        $inherited_from = new DOMElement('tag');
        $inherited->getDocBlock()->getNode()->appendChild($inherited_from);
        $inherited_from->setAttribute('name', 'inherited_from');
        $inherited_from->setAttribute(
            'refers',
            $inherited->getReferrerString($this->class->getFQCN())
        );
        $inherited_from->setAttribute(
            'description',
            $inherited->getReferrerString($this->class->getFQCN())
        );

        // should not duplicate @todo; it only belongs to the original instance
        $inherited->getDocBlock()->filterTags('todo');
    }

    /**
     * Returns the full string identifier of this property.
     *
     * Example:
     *
     *     MyClass::$property
     *
     * @param string|null $parent_class_name The class name to use; if null
     *     uses the current class name.
     *
     * @return string
     */
    public function getReferrerString($parent_class_name = null)
    {
        if ($parent_class_name === null)
        {
            $parent_class_name = $this->class->getFQCN();
        }

        return $parent_class_name . '::$' . $this->getName();
    }

    /**
     * Inherits the properties of an element in another class.
     *
     * @param DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Property $parent
     *
     * @return void
     */
    public function inherit($parent)
    {
        $docblock = $this->getDocBlock();
        $docblock->inherited_tags = array_merge(
            $docblock->inherited_tags,
            $this->inherited_tags
        );
        $docblock->inherit($parent->getDocBlock());

        $this->getNode()->appendChild(
            new DOMElement('override_from', $this->class->getFQCN())
        );
    }

}