<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @category  phpDocumentor
 * @package   Transformer
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

/**
 * Model representing a loaded template.
 *
 * @category phpDocumentor
 * @package  Transformer
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://phpdoc.org
 */
class phpDocumentor_Transformer_Template extends phpDocumentor_Transformer_Abstract
    implements ArrayAccess, Countable, Iterator
{
    /** @var string Name for this template */
    protected $name = null;

    /** @var string */
    protected $author = '';

    /** @var string */
    protected $version = '';

    /** @var string */
    protected $copyright = '';

    /** @var string */
    protected $path = '';

    /** @var phpDocumentor_Transformer_Transformation */
    protected $transformations = array();

    /**
     * Initializes this object with a name and optionally with contents.
     *
     * @param string $name Name for this template.
     * @param string $path The location of the template on this server.
     */
    public function __construct($name, $path)
    {
        $this->name = $name;
        $this->path = $path;
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
     * Returns the location of this template.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Sets the version number for this template.
     *
     * @param string $version Semantic version number in this format: 1.0.0
     *
     * @return void
     */
    public function setVersion($version)
    {
        if (!preg_match('/^\d+\.\d+\.\d+$/', $version)) {
            throw new InvalidArgumentException(
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
     * Populates this template from an XML source.
     *
     * @param phpDocumentor_Transformer $transformer The transformer which is parent.
     * @param string              $xml         The XML definition for this template.
     *
     * @return void
     */
    public function populate(phpDocumentor_Transformer $transformer, $xml)
    {
        $xml = new SimpleXMLElement($xml);
        $this->author    = $xml->author;
        $this->version   = $xml->version;
        $this->copyright = $xml->copyright;

        foreach ($xml->transformations->transformation as $transformation) {
            $transformation_obj = new phpDocumentor_Transformer_Transformation(
                $transformer,
                (string)$transformation['query'],
                (string)$transformation['writer'],
                (string)$transformation['source'],
                (string)$transformation['artifact']
            );

            if (isset($transformation->parameters)
                && count($transformation->parameters)
            ) {
                $transformation_obj->importParameters(
                    $transformation->parameters
                );
            }

            $this->transformations[] = $transformation_obj;
        }
    }

    /**
     * Sets a transformation at the given offset.
     *
     * @param integer|string                     $offset The offset to place
     *  the value at.
     * @param phpDocumentor_Transformer_Transformation $value  The transformation to
     *  add to this template.
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (!$value instanceof phpDocumentor_Transformer_Transformation) {
            throw new InvalidArgumentException(
                'phpDocumentor_Transformer_Template may only contain items of '
                . 'type phpDocumentor_Transformer_Transformation'
            );
        }

        $this->transformations[$offset] = $value;
    }

    /**
     * Gets the transformation at the given offset.
     *
     * @param integer|string $offset The offset to retrieve from.
     *
     * @return phpDocumentor_Transformer_Transformation
     */
    function offsetGet($offset)
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
     * @return scalar scalar on success, integer 0 on failure.
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
     * @return phpDocumentor_Transformer_Transformation
     */
    public function current()
    {
        return current($this->transformations);
    }

}
