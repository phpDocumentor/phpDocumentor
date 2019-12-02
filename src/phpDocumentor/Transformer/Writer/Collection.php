<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Writer;

use ArrayObject;
use InvalidArgumentException;
use phpDocumentor\Transformer\Router\Router;
use function preg_match;

/**
 * A collection of Writer objects.
 *
 * In this collection we can receive writers.
 *
 * In addition this class can also verify if all requirements for the various writers in it are met.
 */
class Collection extends ArrayObject
{
    /** @var Router A series of routers, in order of importance, that are used to generate urls with */
    private $router;

    /**
     * Initializes this writer collection with the necessary requirements.
     *
     * @param Router $router A series of routers, in order of importance, that are used to generate urls with.
     */
    public function __construct(Router $router)
    {
        $this->router = $router;

        parent::__construct();
    }

    /**
     * Registers a writer with a given name.
     *
     * @param string $index a Writer's name, must be at least 3
     *      characters, alphanumeric and/or contain one or more hyphens,
     *      underscores and forward slashes.
     * @param WriterAbstract $newval The Writer object to register to this name.
     *
     * @throws InvalidArgumentException If either of the above restrictions is not met.
     */
    public function offsetSet($index, $newval) : void
    {
        if (!$newval instanceof WriterAbstract) {
            throw new InvalidArgumentException(
                'The Writer Collection may only contain objects descending from WriterAbstract'
            );
        }

        if (!preg_match('/^[a-zA-Z0-9\-\_\/]{3,}$/', $index)) {
            throw new InvalidArgumentException(
                'The name of a Writer may only contain alphanumeric characters, one or more hyphens, underscores and '
                . 'forward slashes and must be at least three characters wide'
            );
        }

        parent::offsetSet($index, $newval);
    }

    /**
     * Retrieves a writer from the collection.
     *
     * @param string $index the name of the writer to retrieve.
     *
     * @throws InvalidArgumentException If the writer is not in the collection.
     */
    public function offsetGet($index) : WriterAbstract
    {
        if (!$this->offsetExists($index)) {
            throw new InvalidArgumentException('Writer "' . $index . '" does not exist');
        }

        return parent::offsetGet($index);
    }

    /**
     * Iterates over each writer in this collection and checks its requirements.
     *
     * @throws Exception\RequirementMissing If a requirement of a writer is missing.
     */
    public function checkRequirements() : void
    {
        /** @var WriterAbstract $writer */
        foreach ($this as $writer) {
            $writer->checkRequirements();
        }
    }
}
