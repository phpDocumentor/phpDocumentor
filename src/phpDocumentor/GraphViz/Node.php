<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @category  phpDocumentor
 * @package   Core
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

/**
 * Class representing a node / element in a graph.
 *
 * @category  phpDocumentor
 * @package   GraphViz
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */
class phpDocumentor_GraphViz_Node
{

    /** @var string Name for this node */
    protected $name = '';

    /** @var phpDocumentor_GraphViz_Attribute[] List of attributes for this node */
    protected $attributes = array();

    /**
     * Creates a new node with name and optional label.
     *
     * @param string      $name  Name of the new node.
     * @param string|null $label Optional label text.
     */
    function __construct($name, $label = null)
    {
        $this->setName($name);
        if ($label !== null) {
            $this->setLabel($label);
        }
    }

    /**
     * Factory method used to assist with fluent interface handling.
     *
     * See the examples for more details.
     *
     * @param string      $name  Name of the new node.
     * @param string|null $label Optional label text.
     *
     * @return phpDocumentor_GraphViz_Node
     */
    public static function create($name, $label = null)
    {
        return new self($name, $label);
    }

    /**
     * Sets the name for this node.
     *
     * Not to confuse with the label.
     *
     * @param string $name Name for this node.
     *
     * @return phpDocumentor_GraphViz_Node
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Returns the name for this node.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Magic method to provide a getter/setter to add attributes on the Node.
     *
     * Using this method we make sure that we support any attribute without
     * too much hassle. If the name for this method does not start with get or
     * set we return null.
     *
     * Set methods return this graph (fluent interface) whilst get methods
     * return the attribute value.
     *
     * @param string  $name      Method name; either getX or setX is expected.
     * @param mixed[] $arguments List of arguments; only 1 is expected for setX.
     *
     * @return phpDocumentor_GraphViz_Attribute[]|phpDocumentor_GraphViz_Node|null
     */
    function __call($name, $arguments)
    {
        $key = strtolower(substr($name, 3));
        if (strtolower(substr($name, 0, 3)) == 'set') {
            $this->attributes[$key] = new phpDocumentor_GraphViz_Attribute(
                $key, $arguments[0]
            );
            return $this;
        }

        if (strtolower(substr($name, 0, 3)) == 'get') {
            return $this->attributes[$key];
        }

        return null;
    }

    /**
     * Returns the node definition as is requested by GraphViz.
     *
     * @return string
     */
    public function __toString()
    {
        $attributes = array();
        foreach ($this->attributes as $value) {
            $attributes[] = (string)$value;
        }
        $attributes = implode("\n", $attributes);

        $name = addslashes($this->getName());

        return <<<DOT
"{$name}" [
$attributes
]
DOT;
    }
}
