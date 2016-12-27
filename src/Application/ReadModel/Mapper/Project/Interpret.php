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

namespace phpDocumentor\Application\ReadModel\Mapper\Project;

use phpDocumentor\DomainModel\ReadModel\Mapper\Project\Interpret as InterpretInterface;
use phpDocumentor\DomainModel\ReadModel\Mapper\Project\Interpreter;
use phpDocumentor\Reflection\Types\Context;

final class Interpret implements InterpretInterface
{
    /** @var mixed */
    private $subject;

    /** @var Interpreter|null */
    private $interpreter;

    /** @var Context */
    private $context;

    /**
     * InterpretCommand constructor.
     *
     * @param mixed $subject
     * @param Context $withContext
     */
    public function __construct($subject, Context $withContext = null)
    {
        $this->subject = $subject;
        $this->context = $withContext;
    }

    /**
     * @return mixed
     */
    public function subject()
    {
        return $this->subject;
    }

    /**
     * @return Interpreter|null
     */
    public function interpreter()
    {
        return $this->interpreter;
    }

    /**
     * @return Context
     */
    public function context()
    {
        return $this->context;
    }

    /**
     * @param Interpreter $interpreter
     *
     * @return Interpret
     */
    public function usingInterpreter(Interpreter $interpreter)
    {
        $command = clone $this;
        $command->interpreter = $interpreter;
        return $command;
    }
}
