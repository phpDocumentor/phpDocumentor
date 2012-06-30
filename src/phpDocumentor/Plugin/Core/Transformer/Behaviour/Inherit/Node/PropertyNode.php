<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @category   phpDocumentor
 * @package    Transformer
 * @subpackage Behaviour
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Core\Transformer\Behaviour\Inherit\Node;

/**
 * Responsible for adding inheritance behaviour to an individual property.
 *
 * @category   phpDocumentor
 * @package    Transformer
 * @subpackage Behaviour
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */
class PropertyNode extends NodeAbstract
{
    /** @var string[] Defined which tags to inherit */
    protected $inherited_tags = array('var');

    /** @var ClassNode */
    protected $class = null;

    /**
     * Load the node belonging to this object, make an associative array of
     * classes and interfaces available and define the parent class.
     *
     * @param \DOMElement $node   Node to decorate.
     * @param array       &$nodes List of classes/interface elements
     * @param ClassNode   $class  Class to which this property belongs.
     */
    public function __construct(
        \DOMElement $node, array &$nodes, ClassNode $class
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
     * @param ClassNode $class_or_interface Object to copy this property onto.
     *
     * @return void
     */
    function copyTo(ClassNode $class_or_interface)
    {
        // self returns this specific class and not the class of the current
        // object; thus we use get_class to retrieve it.
        $class = get_class($this);

        /** @var PropertyNode $inherited */
        $inherited = new $class(
            clone $this->getNode(), $this->nodes, $class_or_interface
        );

        $class_or_interface->getNode()->appendChild($inherited->getNode());

        $inherited->getNode()->appendChild(
            new \DOMElement('inherited_from', $this->class->getFQCN())
        );

        // store the origin of this element
        $inherited_from = new \DOMElement('tag');
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

        // should not duplicate @todo or @deprecated; it only belongs
        // to the original instance
        $inherited->getDocBlock()->filterTags('todo');
        $inherited->getDocBlock()->filterTags('deprecated');
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
        if ($parent_class_name === null) {
            $parent_class_name = $this->class->getFQCN();
        }

        return $parent_class_name . '::$' . $this->getName();
    }

    /**
     * Inherits the properties of an element in another class.
     *
     * @param PropertyNode $parent parent property to inherit from.
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
            new \DOMElement('override_from', $this->class->getFQCN())
        );
    }

}