<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category  DocBlox
 * @package   Transformer
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://docblox-project.org
 */

/**
 * Model representing a loaded template.
 *
 * @category DocBlox
 * @package  Transformer
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://docblox-project.org
 */
class DocBlox_Transformer_Template implements ArrayAccess, Countable, Traversable
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

    /** @var DocBlox_Transformer_Transformation */
    protected $transformations = array();

    /**
     * Initializes this object with a name and optionally with contents.
     *
     * @param string $name Name for this template.
     * @param string $path The location of the template on this server.
     * @param mixed  $data Array with settings to populate this template with.
     */
    public function __construct($name, $path, $data = null)
    {
        $this->name = $name;
        $this->path = $path;

        if ($data !== null)
        {
            $this->populate($data);
        }
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
     * @param string $author
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
     * @param string $copyright
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
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Populates this template from an XML source.
     *
     * @param string $xml
     *
     * @return void
     */
    public function populate($xml)
    {
        $xml = new SimpleXMLElement($xml);
        $this->author    = $xml->author;
        $this->version   = $xml->version;
        $this->copyright = $xml->copyright;

        foreach($xml->transformations as $transformation)
        {
            $this->transformations[] = new DocBlox_Transformer_Transformation(
                null,
                $transformation['query'],
                $transformation['writer'],
                $transformation['source'],
                $transformation['artifact']
            );
        }
    }

    /**
     * Sets a transformation at the given offset.
     *
     * @param integer|string                     $offset The offset to place
     *  the value at.
     * @param DocBlox_Transformer_Transformation $value The transformation to
     *  add to this template.
     *
     * @return void
     */
    public function offsetSet($offset, DocBlox_Transformer_Transformation $value)
    {
        $this->transformations[$offset] = $value;
    }

    /**
     * Gets the transformation at the given offset.
     *
     * @param integer|string $offset The offset to retrieve from.
     *
     * @return DocBlox_Transformer_Transformation
     */
    function offsetGet($offset)
    {
        return $this->transformations[$offset];
    }

    /**
     * Offset to unset.
     *
     * @param integer|string $offset
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

}
