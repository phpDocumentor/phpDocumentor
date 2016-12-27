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

use phpDocumentor\Reflection\Types\Context;

interface Interpret
{
    /**
     * @return mixed
     */
    public function subject();

    /**
     * @return Interpreter|null
     */
    public function interpreter();

    /**
     * @return Context
     */
    public function context();

    /**
     * @param Interpreter $interpreter
     *
     * @return self
     */
    public function usingInterpreter(Interpreter $interpreter);
}
