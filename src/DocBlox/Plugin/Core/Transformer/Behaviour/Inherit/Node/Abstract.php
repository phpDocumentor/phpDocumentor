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

    /** @var string[] All class tags that are inherited when none are defined */
    protected $inherited_tags = array(
        'version',
        'copyright',
        'author'
    );

    /**
     * Initialize the inheritance for this node.
     *
     * @param DOMElement $node
     */
    public function __construct(DOMElement $node)
    {
        $this->node = $node;
    }

    /**
     * Returns the name of the given method or property node.
     *
     * @return string
     */
    protected function getNodeName()
    {
        return current($this->getDirectElementsByTagName($this->node, 'name'))
            ->nodeValue;
    }

    /**
     * Returns the docblock element for the given node; if none exists it will
     * be added.
     *
     * @return DOMElement
     */
    protected function getDocBlockElement()
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

        return $docblock;
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
     * @param DOMElement $node
     * @param string     $element_name
     *
     * @return DOMElement[]
     */
    protected function getDirectElementsByTagName(DOMElement $node, $element_name)
    {
        $result   = array();
        if (!$node->hasChildNodes()) {
            return $result;
        }

        $elements = $node->childNodes;
        for($i = 0; $i < $elements->length; $i++) {
            $element = $elements->item($i);
            if ($element->nodeName != $element_name) {
                continue;
            }

            $result[] = $element;
        }

        return $result;
    }

    /**
     * Copies the short description from the Super element's DocBlock to the
     * Sub element's DocBlock if the sub element has none.
     *
     * @param DOMElement $super_docblock
     * @param DOMElement $docblock
     *
     * @return void
     */
    public function copyShortDescription(DOMElement $super_docblock,
        DOMElement $docblock)
    {
        /** @var DOMElement $desc  */
        $desc = current($this->getDirectElementsByTagName($docblock, 'description'));

        $super_desc = current(
            $this->getDirectElementsByTagName($super_docblock, 'description')
        );

        if ((($desc === false) || (!trim($desc->nodeValue)))
            && ($super_desc !== false)
        ) {
            if ($desc !== false) {
                $docblock->removeChild($desc);
            }

            $docblock->appendChild(clone $super_desc);
        } elseif ($desc && $super_desc) {
            // if a short description exists in both child and parent; insert the
            // parent's SD when the inline tag {@inheritdoc} is used.
            $desc->nodeValue = htmlspecialchars(
                str_ireplace(
                    '{@inheritdoc}',
                    $super_desc->nodeValue,
                    $desc->nodeValue
                )
            );
        }
    }

    /**
     * Copies the long description from the Super element's DocBlock to the
     * Sub element's DocBlock if the sub element has none.
     *
     * @param DOMElement $super_docblock
     * @param DOMElement $docblock
     *
     * @return void
     */
    public function copyLongDescription(DOMElement $super_docblock,
        DOMElement $docblock)
    {
        /** @var DOMElement $desc  */
        $desc = current(
            $this->getDirectElementsByTagName($docblock, 'long-description')
        );
        $short_desc = current(
            $this->getDirectElementsByTagName($docblock, 'description')
        );

        $super_desc = current(
            $this->getDirectElementsByTagName($super_docblock, 'long-description')
        );

        if ((($desc === false) || (!trim($desc->nodeValue)))
            && ($super_desc !== false)
        ) {
            if ($desc !== false) {
                $docblock->removeChild($desc);
            }

            $docblock->appendChild(clone $super_desc);
        } elseif ($desc && $super_desc) {

            // if the short description equals {@inheritdoc}, copy the long
            // description as well.
            if (strtolower($short_desc->nodeValue) == '{@inheritdoc}') {
                $desc->nodeValue = $super_desc->nodeValue
                    . "\n" . $desc->nodeValue;
            } else {
                // if a long description exists in both child and parent; insert the
                // parent's LD when the inline tag {@inheritdoc} is used.
                $desc->nodeValue = htmlspecialchars(
                    str_ireplace(
                        '{@inheritdoc}',
                        $super_desc->nodeValue,
                        $desc->nodeValue
                    )
                );
            }
        }
    }

    /**
     * Copies the tags from the super docblock to this one if it matches
     * the criteria.
     *
     * Criteria for copying are:
     *
     * * Tag name must be in the list of to-be-copied tag names
     * * No tag with that name may be in the sub element
     *
     * @param string[]   $tag_types      array of to-be-copied tag names.
     * @param DOMElement $super_docblock DocBlock of the super element.
     * @param DOMElement $docblock       DocBlock of the sub element.
     *
     * @return void
     */
    protected function copyTags(array $tag_types, DOMElement $super_docblock,
        DOMElement $docblock)
    {
        // get the names of all existing tags because we should only add
        // parent tags if there are none in the existing docblock
        $existing_tag_names = array();
        foreach ($this->getDirectElementsByTagName($docblock, 'tag') as $tag)
        {
            $existing_tag_names[] = $tag->getAttribute('name');
        }
        $existing_tag_names = array_unique($existing_tag_names);

        /** @var DOMElement $tag */
        foreach ($this->getDirectElementsByTagName($super_docblock, 'tag') as $tag) {
            $tag_name = $tag->getAttribute('name');

            if (in_array($tag_name, $tag_types)
                && (!in_array($tag_name, $existing_tag_names))
            ) {
                $child = clone $tag;
                $child->setAttribute('line', $this->node->getAttribute('line'));
                $docblock->appendChild($child);
            }
        }
    }

    /**
     * Combines the docblock of an overridden method with this one if applicable.
     *
     * @param mixed[]    $super        Array containing a flat list of methods for
     *     a tree of inherited classes.
     * @param string     $class_name   Name of the current class.
     *
     * @return void
     */
    public function apply(array &$super, $class_name)
    {
        // the name is always the first encountered child element with
        // tag name 'name'
        $node_name = $this->getNodeName();

        // only process if the super has a node with this name
        if (isset($super[$node_name])) {
            $docblock = $this->getDocBlockElement();

            /** @var DOMElement $super_object  */
            $super_object = $super[$node_name]['object'];
            $super_class  = $super[$node_name]['class'];

            // add an element which defines which class' element you override
            $this->node->appendChild(new DOMElement('overrides-from', $super_class));

            /** @var DOMElement $super_docblock  */
            $super_docblock = current(
                $this->getDirectElementsByTagName($super_object, 'docblock')
            );

            // only copy the docblock info when it is present in the superclass
            if ($super_docblock)
            {
                // first long, then short in order for the {@inheritdoc} to
                // function properly
                $this->copyLongDescription($super_docblock, $docblock);
                $this->copyShortDescription($super_docblock, $docblock);
                $this->copyTags($this->inherited_tags, $super_docblock, $docblock);
            }
        }

        // store the element in the super array; we use this in the Class
        // inheritance to add 'inherited' methods (methods not present in this
        // class by definition but injected via a superclass)
        $super[$node_name] = array(
            'class' => $class_name,
            'object' => $this->node
        );
    }

}