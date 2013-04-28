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

namespace phpDocumentor\Transformer\Writer;

/**
 * Collection object for a set of Writers.
 */
use phpDocumentor\Transformer\Router\Queue;

class Collection extends \ArrayObject
{
    protected $routers;

    public function __construct(Queue $routers)
    {
        $this->routers = $routers;

        parent::__construct();
    }

    /**
     * Registers a writer with a given name.
     *
     * @param string         $index a Writer's name, must be at least 3
     *     characters, alphanumeric and/or contain one or more hyphens,
     *     underscores and forward slashes.
     * @param WriterAbstract $newval The Writer object to register to this name.
     *
     * @throws \InvalidArgumentException if either of the above restrictions is
     *     not met.
     *
     * @return void
     */
    public function offsetSet($index, $newval)
    {
        if (!$newval instanceof WriterAbstract) {
            throw new \InvalidArgumentException(
                'The Writer Collection may only contain objects descending from WriterAbstract'
            );
        }

        if (!preg_match('/^[a-zA-Z0-9\-\_\/]{3,}$/', $index)) {
            throw new \InvalidArgumentException(
                'The name of a Writer may only contain alphanumeric characters, one or more hyphens, underscores and '
                .'forward slashes and must be at least three characters wide'
            );
        }

        // if the writer supports routes, provide them with the router queue
        if ($newval instanceof Routable) {
            $newval->setRouters($this->routers);
        }

        parent::offsetSet($index, $newval);
    }

    /**
     * Retrieves a writer from the collection.
     *
     * @param string $index the name of the writer to retrieve.
     *
     * @throws \InvalidArgumentException if the writer is not in the collection.
     *
     * @return WriterAbstract
     */
    public function offsetGet($index)
    {
        if (!$this->offsetExists($index)) {
            throw new \InvalidArgumentException('Writer "' . $index .'" does not exist');
        }

        return parent::offsetGet($index);
    }
}
