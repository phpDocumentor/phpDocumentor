<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Partials;

use Parsedown;
use phpDocumentor\Descriptor\Collection as DescriptorCollection;

/**
 * Represents an easily accessible collection of partials.
 */
class Collection extends DescriptorCollection
{
    /** @var Parsedown $parser */
    protected $parser = null;

    /**
     * Constructs a new collection object.
     */
    public function __construct(Parsedown $parser)
    {
        parent::__construct();
        $this->parser = $parser;
    }

    /**
     * Sets a new object onto the collection or clear it using null.
     *
     * @param string|integer $index An index value to recognize this item with.
     * @param mixed          $item  The item to store, generally a Descriptor but may be something else.
     */
    public function set($index, $item): void
    {
        $this->offsetSet($index, $this->parser->text($item));
    }
}
