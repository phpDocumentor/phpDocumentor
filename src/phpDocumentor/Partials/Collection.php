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

namespace phpDocumentor\Partials;

use phpDocumentor\Descriptor\Collection as DescriptorCollection;

/**
 * Represents an easily accessible collection of partials.
 */
class Collection extends DescriptorCollection
{
    /** @var \Parsedown $parser */
    protected $parser = null;

    /**
     * Constructs a new collection object.
     *
     * @param \Parsedown $parser
     */
    public function __construct(\Parsedown $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Sets a new object onto the collection or clear it using null.
     *
     * @param string|integer $index An index value to recognize this item with.
     * @param string         $item  The item to store, generally a Descriptor but may be something else.
     *
     * @return void
     */
    public function set($index, $item)
    {
        $this->offsetSet($index, $this->parser->text($item));
    }
}
