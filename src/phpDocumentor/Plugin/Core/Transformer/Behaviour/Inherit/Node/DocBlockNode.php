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
 * Wrapper class around a DOMElement containing a DocBlock definition.
 *
 * This class provides accessors for a DOMElement that contains a DocBlock
 * definition and methods to filter and inherit DocBlocks.
 *
 * @category   phpDocumentor
 * @package    Transformer
 * @subpackage Behaviour
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */
class DocBlockNode extends NodeAbstract
{
    public $inherited_tags = array('author', 'copyright', 'version');

    /**
     * Returns the Node of the short description for this DocBlock.
     *
     * To get the value append`->nodeValue` to the result of this method.
     *
     * @return \DOMElement|bool
     */
    public function getShortDescription()
    {
        return current(
            $this->getDirectElementsByTagName($this->node, 'description')
        );
    }

    /**
     * Returns the Node of the long description for this DocBlock.
     *
     * To get the value append`->nodeValue` to the result of this method.
     *
     * @return \DOMElement|bool
     */
    public function getLongDescription()
    {
        return current(
            $this->getDirectElementsByTagName($this->node, 'long-description')
        );
    }

    /**
     * Determines whether the short description should be inherited from the
     * given parent description.
     *
     * @param \DOMElement|bool $super_desc The short description found in the
     *     parent DocBlock.
     *
     * @return bool
     */
    protected function shouldInheritShortDescription($super_desc)
    {
        $desc = $this->getShortDescription();
        $has_no_description = (($desc === false) || (!trim($desc->nodeValue)));

        return ($has_no_description && ($super_desc !== false));
    }

    /**
     * Determines whether the long description should be inherited from the
     * given parent description.
     *
     * @param \DOMElement|bool $super_desc The long description found in the
     *     parent DocBlock.
     *
     * @return bool
     */
    protected function shouldInheritLongDescription($super_desc)
    {
        $desc = $this->getLongDescription();
        $has_no_description = (($desc === false) || (!trim($desc->nodeValue)));

        return ($has_no_description && ($super_desc !== false));
    }

    /**
     * Inherits the short description from the given parent DocBlock.
     *
     * @param DocBlockNode $parent Node to inherit the short description from.
     *
     * @return void
     */
    protected function inheritShortDescription(DocBlockNode $parent)
    {
        $desc = $this->getShortDescription();
        $super_desc = $parent->getShortDescription();

        if ($this->shouldInheritShortDescription($super_desc)) {
            if ($desc !== false) {
                $this->node->removeChild($desc);
            }
            $this->node->appendChild(clone $super_desc);
        } elseif ($desc && $super_desc) {
            // if a short description exists in both child and parent; insert the
            // parent's SD when the inline tag {@inheritdoc} is used.
            $desc->nodeValue = htmlspecialchars(
                str_ireplace(
                    '{@inheritdoc}', $super_desc->nodeValue, $desc->nodeValue
                )
            );
        }
    }

    /**
     * Inherits the long description from the given parent DocBlock.
     *
     * @param DocBlockNode $parent parent docblock whose long description to
     *     inherit.
     *
     * @return void
     */
    protected function inheritLongDescription(DocBlockNode $parent)
    {
        $short_desc = $this->getShortDescription();
        $desc       = $this->getLongDescription();
        $super_desc = $parent->getLongDescription();

        if ($this->shouldInheritLongDescription($super_desc)) {
            if ($desc !== false) {
                $this->node->removeChild($desc);
            }

            $this->node->appendChild(clone $super_desc);
        } elseif ($desc && $super_desc) {
            // if the short description equals {@inheritdoc}, copy the long
            // description as well.
            if (strtolower($short_desc->nodeValue) == '{@inheritdoc}') {
                $desc->nodeValue = $super_desc->nodeValue . "\n" . $desc->nodeValue;
            } else {
                // if a long description exists in both child and parent; insert the
                // parent's LD when the inline tag {@inheritdoc} is used.
                $desc->nodeValue = htmlspecialchars(
                    str_ireplace(
                        '{@inheritdoc}', $super_desc->nodeValue, $desc->nodeValue
                    )
                );
            }
        }
    }

    /**
     * Returns all DOMElements representing tags.
     *
     * @return \DOMElement[]
     */
    public function getTags()
    {
        return $this->getDirectElementsByTagName($this->node, 'tag');
    }

    /**
     * Returns all DOMElements representing tags with the given name.
     *
     * @param string $name Name of the tag type to find.
     *
     * @return DOMElement[]
     */
    public function getTagsByName($name)
    {
        $result = array();
        foreach ($this->getTags() as $tag) {
            if ($tag->getAttribute('name') == $name) {
                $result[] = $tag;
            }
        }

        return $result;
    }

    /**
     * Removes all tags with a given name from this DocBlock.
     *
     * @param string $name Name of the tag type to remove.
     *
     * @return void
     */
    public function filterTags($name)
    {
        $removal = array();
        foreach ($this->getTags() as $tag) {
            if ($tag->getAttribute('name') == $name) {
                $removal[] = $tag;
            }
        }

        // separate in two steps because of unexpected behaviour DOMNodeList
        foreach ($removal as $tag) {
            $tag->parentNode->removeChild($tag);
        }
    }

    /**
     * Removes the `subpackage` index from the given list of tags if the `package`
     * of this class does not match its parent.
     *
     * @param DocBlockNode $parent Parent Docblock to check against.
     * @param string[]     &$tags  Array of inheritable tags to filter.
     *
     * @return void
     */
    protected function filterSubpackageInheritance(
        DocBlockNode $parent, array &$tags
    ) {
        // find the name of the super's package
        /** @var \DOMElement $super_package  */
        $super_package = current($parent->getTagsByName('package'));
        $super_package_name = $super_package
                ? $super_package->getAttribute('description') : null;

        /** @var \DOMElement $local_package  */
        $local_package = current($this->getTagsByName('package'));
        $local_package_name = $local_package
                ? $local_package->getAttribute('description') : null;

        // if the package names do not match; do not inherit the subpackage
        if ($super_package_name != $local_package_name) {
            foreach ($tags as $key => $type) {
                if ($type == 'subpackage') {
                    unset($tags[$key]);
                    break;
                }
            }
        }
    }

    /**
     * Copies tag nodes from the given $parent to this DocBlock if the name
     * occurs in the given $tags array.
     *
     * @param DocBlockNode $parent Parent node to inherit tags from.
     * @param string[]     $tags   List of inheritable tags.
     *
     * @return void
     */
    protected function inheritTags(DocBlockNode $parent, array $tags)
    {
        $this->filterSubpackageInheritance($parent, $tags);

        // get the names of all existing tags because we should only add
        // parent tags if there are none in the existing docblock
        $existing_tag_names = array();
        foreach ($this->getTags() as $tag) {
            $existing_tag_names[] = $tag->getAttribute('name');
        }
        $existing_tag_names = array_unique($existing_tag_names);

        /** @var \DOMElement $tag */
        foreach ($parent->getTags() as $tag) {
            $tag_name = $tag->getAttribute('name');

            if (in_array($tag_name, $tags)
                && (!in_array($tag_name, $existing_tag_names))
            ) {
                $child = clone $tag;
                $child->setAttribute('line', $this->node->getAttribute('line'));
                $this->node->appendChild($child);
            }
        }
    }

    /**
     * Inherits the short description, long description and tags of the given
     * $parent node.
     *
     * @param DocBlockNode $parent Parent nodeblock to inherit.
     *
     * @return void
     */
    public function inherit($parent)
    {
        $this->inheritShortDescription($parent);
        $this->inheritLongDescription($parent);
        $this->inheritTags($parent, $this->inherited_tags);
    }
}
