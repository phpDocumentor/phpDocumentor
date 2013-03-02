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

namespace phpDocumentor\Descriptor;

/**
 * Represents an easily accessible collection of elements with support for adding elements by references.
 *
 * The goal for this class is to allow Descriptors to be easily retrieved and set so that interaction in
 * templates becomes easier.
 *
 * Additionally this class provides extended support for storing references as these are integral to the
 * incremental processing of elements. Every element is stored as a reference and when an element is deleted
 * then all references should be deleted automatically as well.
 *
 * With regards to references this collection offers:
 *
 * * Specialized methods to store and destroy references.
 * * To allow for recursive removal of references inside references (to prevent orphaning, see
 *   {@see Interfaces\ReferencingInterface} for more information)
 * * Automated garbage collection when serializing; this removes all elements with a value of `null`.
 *
 * The developer's manual contains information specific to how incremental processing is achieved in
 * phpDocumentor.
 */
class Collection implements \Countable, \IteratorAggregate, \ArrayAccess, Interfaces\ReferencingInterface
{
    /** @var mixed[] $items */
    protected $items = array();

    /**
     * Constructs a new collection object with optionally a series of items, generally Descriptors.
     *
     * @param DescriptorAbstract[]|mixed[] $items
     */
    public function __construct($items = array())
    {
        $this->items = $items;
    }

    /**
     * Adds a new item to this collection, generally a Descriptor.
     *
     * Please note that this method does not store the item as a reference and should thus not be used as part of
     * the incremental processing mechanism.
     *
     * @param DescriptorAbstract|mixed $item
     *
     * @see self::setReference() to add references to objects as part of the incremental processing technique.
     *
     * @return void
     */
    public function add($item)
    {
        $this->items[] = $item;
    }

    /**
     * Sets a new object onto the collection or clear it using null.
     *
     * Please note that this method does not store the item as a reference and should thus not be used as part of
     * the incremental processing mechanism.
     *
     * @param string|integer                $index An index value to recognize this item with.
     * @param DescriptorAbstract|mixed|null $item  The item to store, generally a Descriptor but may be something else.
     *
     * @see self::setReference() to add references to objects as part of the incremental processing technique.
     *
     * @return void
     */
    public function set($index, $item)
    {
        $this->offsetSet($index, $item);
    }

    /**
     * Sets a new reference to an object onto the collection.
     *
     * Please note that to destroy the object to which the reference point you should use the
     * {@see set()} method with a value of `null`. Unsetting the index will not result in
     * destruction of the item but the removal from this collection.
     *
     * Example:
     *
     *     If I have an index containing class references, and an `extends` property that has a reference to a specific
     *     class; then to remove that class from the application you should set the class reference to `null` in the
     *     class index and _then_ unset the entry.
     *
     *     If the entry were to be unset without applying `null` first then the `extends` property would still contain
     *     a reference to that class.
     *
     * @param string|integer $index
     * @param mixed          &$item
     *
     * @throws \InvalidArgumentException if the key is null or an empty string.
     *
     * @return void
     */
    public function setReference($index, &$item)
    {
        if ($index === '' || $index === null) {
            throw new \InvalidArgumentException('The key of a collection must always be set');
        }

        $this->items[$index] = &$item;
    }

    /**
     * Destroys a reference and clears any dependent sub-references.
     *
     * @param string|integer $index
     *
     * @return void
     */
    public function destroyReference($index)
    {
        if ($this->items[$index] instanceof Interfaces\ReferencingInterface) {
            $this->items[$index]->clearReferences();
        }

        $this->offsetSet($index, null);
        $this->offsetUnset($index);
    }

    /**
     * Retrieves a specific item from the Collection with its index.
     *
     * Please note that this method (intentionally) has the side effect that whenever a key does not exist that it will
     * be created with the value provided by the $valueIfEmpty argument. This will allow for easy initialization during
     * tree building operations.
     *
     * @param string|integer $index
     * @param mixed          $valueIfEmpty If the index does not exist it will be created with this value and returned.
     *
     * @return mixed The contents of the element with the given index and the provided default if the key doesn't exist.
     */
    public function get($index, $valueIfEmpty = null)
    {
        if (!$this->offsetExists($index)) {
            $this->offsetSet($index, $valueIfEmpty);
        }

        return $this->offsetGet($index);
    }

    /**
     * Retrieves all items from this collection as PHP Array.
     *
     * @return mixed[]
     */
    public function getAll()
    {
        return $this->items;
    }

    /**
     * Retrieves an iterator to traverse this object.
     *
     * @return \Traversable|\ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * Returns a count of the number of elements in this collection.
     *
     * @return integer
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * Clears all references (and thus subreferences) and empties the collection.
     *
     * @return void
     */
    public function clear()
    {
        $this->clearReferences();
        $this->items = array();
    }

    /**
     * Retrieves an item as if it were a property of the collection.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * Checks whether an item in this collection exists.
     *
     * @param string|integer $offset The index to check on.
     *
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }

    /**
     * Retrieves an item from the collection with the given index.
     *
     * @param string|integer $offset The offset to retrieve.
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return ($this->offsetExists($offset)) ? $this->items[$offset] : null;
    }

    /**
     * Sets an item at the given index.
     *
     * @param string|integer $offset The offset to assign the value to.
     * @param mixed          $value  The value to set.
     *
     * @throws \InvalidArgumentException if the key is null or an empty string.
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if ($offset === '' || $offset === null) {
            throw new \InvalidArgumentException('The key of a collection must always be set');
        }

        $this->items[$offset] = $value;
    }

    /**
     * Removes an item with the given index from the collection.
     *
     * @param string|integer $offset The offset to unset.
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    /**
     * References to child Descriptors/objects should be assigned a null when the containing object is nulled.
     *
     * In this method should all references to objects be assigned the value null; this will clear the references
     * of child objects from other objects.
     *
     * For example:
     *
     *     A class should NULL its constants, properties and methods as they are contained WITHIN the class and become
     *     orphans if not nulled.
     *
     * @return void
     */
    public function clearReferences()
    {
        foreach ($this->items as &$item) {
            if ($item instanceof Interfaces\ReferencingInterface) {
                $item->clearReferences();
            }

            $item = null;
        }
    }

    /**
     * Garbage collect all null items, these are released references.
     *
     * @return void
     */
    public function collectGarbage()
    {
        foreach ($this->items as $key => $item) {
            if ($item === null) {
                unset($this->items[$key]);
            }
        }
    }

    /**
     * Before serialization, run the garbage collector to make sure that the serialized data is clean.
     *
     * @link http://www.php.net/manual/en/language.oop5.magic.php#object.sleep
     *
     * @return string[] Array with property names to serialize.
     */
    public function __sleep()
    {
        $this->collectGarbage();

        return array('items');
    }
}
