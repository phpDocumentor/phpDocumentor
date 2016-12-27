<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\DomainModel\ReadModel\Mapper\Project;

/**
 * Interpreter for PHP code parsed by PHP-Parser.
 *
 * This class calls a series of reducers; each reducer can do one of three things given an empty state object:
 *
 * - Interpret a PHP-Parser source object (identified as $source), hydrate the given $state object and
 *   return a new version of that $state.
 * - Hand off the parsing to the next reducer if it identifies that it cannot handle the given $source by
 *   calling this interpreter's `next()` method.
 * - Recursively interpret a new PHP-Parser source object by invoking the interpreters `interpret()` method.
 *
 * This class makes use of the Prototype design pattern; meaning that every time that you invoke the `interpret()`
 * method that a new instance is cloned from the previous version and that the list of reducers is rewinded. This
 * will ensure that when you use this object recursively that you have a new state to work with.
 */
final class Interpreter
{
    private $reducers;

    /**
     * Interpreter constructor.
     *
     * @param array $reducers
     */
    public function __construct(array $reducers = [])
    {
        $this->reducers = new \ArrayIterator($reducers);
    }

    public function reducers()
    {
        return $this->reducers;
    }

    public function interpret(Interpret $command, $state = null)
    {
        $chain = clone $this;

        return $chain->executeReducer($command->usingInterpreter($this), $state);
    }

    public function next(Interpret $command, $state = null)
    {
        $this->reducers()->next();

        return $this->executeReducer($command, $state);
    }

    /**
     * Resets the reducers when an object is cloned.
     */
    public function __clone()
    {
        $this->reducers = clone $this->reducers;
        $this->reducers->rewind();
    }

    /**
     * @param Interpret $command
     * @param $state
     *
     * @return
     */
    private function executeReducer(Interpret $command, $state = null)
    {
        $reducer = $this->reducers()->current();
        if ($reducer === null) {
            return $state;
        }

        if (! is_callable($reducer)) {
            return $this->next($command, $state);
        }

        return $this->next($command, $reducer($command, $state));
    }
}
