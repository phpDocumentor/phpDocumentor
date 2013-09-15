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

namespace phpDocumentor\Tutorials;

use phpDocumentor\Descriptor\Collection as DescriptorCollection;

/**
 * Represents an easily accessible collection of partials.
 */
class Collection extends DescriptorCollection
{
    /** @var mixed $parser */
    protected $parser = null;

    /**
     * Constructs a new collection object with optionally a series of items, generally Descriptors.
     *
     * @param DescriptorAbstract[]|mixed[] $items
     */
    public function __construct($items = array())
    {
        $this->parser = new \dflydev\markdown\MarkdownExtraParser;
        $this->items = $items;
    }

    /**
     * Sets a new object onto the collection or clear it using null.
     *
     * @param string|integer                $index An index value to recognize this item with.
     * @param DescriptorAbstract|mixed|null $item  The item to store, generally a Descriptor but may be something else.
     *
     * @return void
     */
    public function set($index, $item)
    {
        $content = is_readable($item) ? file_get_contents($item) : $item;
        $this->offsetSet($index, $this->parser->transformMarkdown($content));
    }

    /**
     * @return mixed
     */
    public function getParser()
    {
        return $parser;
    }

    /**
     * @param mixed $parser
     */
    public function setParser($parser)
    {
        $this->parser = $parser;
    }
}
