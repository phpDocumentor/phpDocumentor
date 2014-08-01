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

namespace phpDocumentor\Plugin\Scrybe\Converter\Metadata\TableOfContents;

/**
 * The Table of Contents entry provides essential information on the current entry, it's place in the structure and
 * where it points to.
 */
abstract class BaseEntry
{
    /**
     * The parent BaseEntry in the containing module.
     *
     * This property may also contain a null value if this is the top entry in this module. Please note that files are
     * also considered entries.
     *
     * Please note that a null value is usually reserved to the ``index`` file.
     *
     * @var BaseEntry|null
     */
    protected $parent = null;

    /**
     * The child entries that are contained in this entry.
     *
     * Any entry may contain any amount of child entries which may either be Headings or Files.
     *
     * @var BaseEntry[]
     */
    protected $children = array();

    /**
     * The heading name, or caption, for this entry.
     *
     * @var string
     */
    protected $name = '';

    /**
     * Initializes this entry with the given parent, if available.
     *
     * @param BaseEntry|null $parent
     */
    public function __construct($parent = null)
    {
        $this->setParent($parent);
    }

    /**
     * Returns the parent entry for this entry.
     *
     * @see $parent for more information rgarding parent entries.
     *
     * @return BaseEntry|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Sets the parent entry for this entry.
     *
     * @param BaseEntry|null $parent
     *
     * @throws \InvalidArgumentException if the given parameter is of an incorrect type.
     *
     * @return void
     */
    public function setParent($parent)
    {
        if ($parent !== null &&  !$parent instanceof BaseEntry) {
            throw new \InvalidArgumentException('An entry may only have another entry as parent');
        }

        $this->parent = $parent;
    }

    /**
     * Returns a list of entries.
     *
     * @see $children for more information regarding child entries.
     *
     * @return BaseEntry[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Adds a child to the collection of children.
     *
     * @param BaseEntry $entry
     *
     * @return void
     */
    public function addChild(BaseEntry $entry)
    {
        $this->children[] = $entry;
    }

    /**
     * Returns the name for this entry.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the caption for this entry,
     *
     * @param string $name
     *
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}
