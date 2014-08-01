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

namespace phpDocumentor\Transformer;

use JMS\Serializer\Annotation as Serializer;
use phpDocumentor\Transformer\Template\Parameter;

/**
 * Model representing a template.
 *
 * @Serializer\XmlRoot("template")
 */
class Template implements \ArrayAccess, \Countable, \Iterator
{
    /**
     * @Serializer\Type("string")
     * @var string Name for this template
     */
    protected $name = null;

    /**
     * @Serializer\Type("string")
     * @var string The name and optionally mail address of the author, i.e. `Mike van Riel <me@mikevanriel.com>`.
     */
    protected $author = '';

    /**
     * @Serializer\Type("string")
     * @var string The version of the template according to semantic versioning, i.e. 1.2.0
     */
    protected $version = '';

    /**
     * @Serializer\Type("string")
     * @var string A free-form copyright notice.
     */
    protected $copyright = '';

    /**
     * @Serializer\Type("string")
     * @var string a text providing more information on this template.
     */
    protected $description = '';

    /**
     * @Serializer\XmlList(entry = "transformation")
     * @Serializer\Type("array<phpDocumentor\Transformer\Transformation>")
     * @var Transformation[] A series of transformations to execute in sequence during transformation.
     */
    protected $transformations = array();

    /**
     * @Serializer\XmlList(entry = "parameter")
     * @Serializer\Type("array<phpDocumentor\Transformer\Template\Parameter>")
     * @var Parameter[] Global parameters that are passed to each transformation.
     */
    protected $parameters = array();

    /**
     * Initializes this object with a name and optionally with contents.
     *
     * @param string $name Name for this template.
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Name for this template.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * The name of the author of this template (optionally including mail
     * address).
     *
     * @param string $author Name of the author optionally including mail address
     *  between angle brackets.
     *
     * @return void
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * Returns the name and/or mail address of the author.
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Sets the copyright string for this template.
     *
     * @param string $copyright Free-form copyright notice.
     *
     * @return void
     */
    public function setCopyright($copyright)
    {
        $this->copyright = $copyright;
    }

    /**
     * Returns the copyright string for this template.
     *
     * @return string
     */
    public function getCopyright()
    {
        return $this->copyright;
    }

    /**
     * Sets the version number for this template.
     *
     * @param string $version Semantic version number in this format: 1.0.0
     *
     * @throws \InvalidArgumentException if the version number is invalid
     * @return void
     */
    public function setVersion($version)
    {
        if (!preg_match('/^\d+\.\d+\.\d+$/', $version)) {
            throw new \InvalidArgumentException(
                'Version number is invalid; ' . $version . ' does not match '
                . 'x.x.x (where x is a number)'
            );
        }
        $this->version = $version;
    }

    /**
     * Returns the version number for this template.
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Sets the description for this template.
     *
     * @param string $description An unconstrained text field where the user can provide additional information
     *     regarding details of the template.
     *
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Returns the description for this template.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets a transformation at the given offset.
     *
     * @param integer|string $offset The offset to place the value at.
     * @param Transformation $value  The transformation to add to this template.
     *
     * @throws \InvalidArgumentException if an invalid item was received
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (!$value instanceof Transformation) {
            throw new \InvalidArgumentException(
                '\phpDocumentor\Transformer\Template may only contain items of '
                . 'type \phpDocumentor\Transformer\Transformation'
            );
        }

        $this->transformations[] = $value;
    }

    /**
     * Gets the transformation at the given offset.
     *
     * @param integer|string $offset The offset to retrieve from.
     *
     * @return Transformation
     */
    public function offsetGet($offset)
    {
        return $this->transformations[$offset];
    }

    /**
     * Offset to unset.
     *
     * @param integer|string $offset Index of item to unset.
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->transformations[$offset]);
    }

    /**
     * Whether a offset exists.
     *
     * @param mixed $offset An offset to check for.
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @return boolean Returns true on success or false on failure.
     */
    public function offsetExists($offset)
    {
        return isset($this->transformations[$offset]);
    }

    /**
     * Count the number of transformations.
     *
     * @link http://php.net/manual/en/countable.count.php
     *
     * @return int The count as an integer.
     */
    public function count()
    {
        return count($this->transformations);
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @link http://php.net/manual/en/iterator.rewind.php
     *
     * @return void
     */
    public function rewind()
    {
        reset($this->transformations);
    }

    /**
     * Checks if current position is valid.
     *
     * @link http://php.net/manual/en/iterator.valid.php
     *
     * @return boolean Returns true on success or false on failure.
     */
    public function valid()
    {
        return (current($this->transformations) === false)
            ? false
            : true;
    }

    /**
     * Return the key of the current element.
     *
     * @link http://php.net/manual/en/iterator.key.php
     *
     * @return int|string scalar on success, integer 0 on failure.
     */
    public function key()
    {
        key($this->transformations);
    }

    /**
     * Move forward to next element.
     *
     * @link http://php.net/manual/en/iterator.next.php
     *
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        next($this->transformations);
    }

    /**
     * Return the current element.
     *
     * @link http://php.net/manual/en/iterator.current.php
     *
     * @return Transformation
     */
    public function current()
    {
        return current($this->transformations);
    }

    /**
     * Returns the parameters associated with this template.
     *
     * @return Parameter[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Sets a new parameter in the collection.
     *
     * @param string|integer $key
     * @param Parameter      $value
     *
     * @return void
     */
    public function setParameter($key, $value)
    {
        $this->parameters[$key] = $value;
    }

    /**
     * Pushes the parameters of this template into the transformations.
     *
     * @return void
     */
    public function propagateParameters()
    {
        foreach ($this->transformations as $transformation) {
            $transformation->setParameters(array_merge($transformation->getParameters(), $this->getParameters()));
        }
    }
}
