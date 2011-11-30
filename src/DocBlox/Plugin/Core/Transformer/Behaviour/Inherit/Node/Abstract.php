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
 * Base class for adding inheritance to an element.
 *
 * @category   DocBlox
 * @package    Transformer
 * @subpackage Behaviour
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
abstract class DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Abstract
{
    /** @var DOMElement */
    protected $node = null;

    /** @var DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Class[] */
    protected $nodes = array();

    /**
     * Load the node belonging to this object and make an associative array of
     * classes and interfaces available.
     *
     * @param DOMElement $node
     * @param DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Class[] $nodes
     */
    public function __construct(DOMElement $node, array &$nodes)
    {
        $this->node = $node;
        $this->nodes = $nodes;
    }

    /**
     * Returns the elements with the given tag name that can be found
     * as direct children of $node.
     *
     * getElementsByTagName returns all DOMElements with the given tag name
     * regardless where in the DOM subtree they are. This method checks whether
     * the parent node matches the given node and thus determines whether it is
     * a direct child.
     *
     * @param DOMElement|InheritClass $node
     * @param string                  $element_name
     *
     * @return DOMElement[]
     */
    protected function getDirectElementsByTagName($node, $element_name)
    {
        $result = array();

        /** @var DOMElement $element */
        foreach ($node->childNodes as $element) {
            if ($element->nodeName != $element_name) {
                continue;
            }

            $result[] = $element;
        }

        return $result;
    }

    /**
     * Derived methods will inherit properties from the parent object into
     * this one.
     *
     * @param DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Abstract $parent
     *
     * @return void
     */
    abstract public function inherit($parent);

    /**
     * Returns the contained node.
     *
     * @return DOMElement
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * Passes all unknown method directly into the node.
     *
     * @param string  $name      Method name.
     * @param mixed[] $arguments Array containing the method arguments.
     *
     * @return mixed
     */
    function __call($name, $arguments)
    {
        return call_user_func_array(array($this->node, $name), $arguments);
    }

    /**
     * Returns the docblock element for the given node; if none exists it will
     * be added.
     *
     * @return DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_DocBlock
     */
    public function getDocBlock()
    {
        $docblocks = $this->getDirectElementsByTagName($this->node, 'docblock');

        // if this method does not yet have a docblock; add one; even
        // though DocBlox throws a warning about a missing DocBlock!
        if (count($docblocks) < 1) {
            $docblock = new DOMElement('docblock');
            $this->node->appendChild($docblock);
        } else {
            /** @var DOMElement $docblock  */
            $docblock = reset($docblocks);
        }

        return new DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_DocBlock(
            $docblock, $this->nodes
        );
    }
}