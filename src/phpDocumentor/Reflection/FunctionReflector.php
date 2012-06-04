<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection;

use phpDocumentor\Reflection\BaseReflector;

/**
 * Provides Static Reflection for functions.
 *
 * @author  Mike van Riel <mike.vanriel@naenius.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    http://phpdoc.org
 */
class FunctionReflector extends BaseReflector
{
    /** @var \PHPParser_Node_Stmt_Function */
    protected $node;

    /** @var FunctionReflector\ArgumentReflector[] */
    protected $arguments = array();

    /**
     * Initializes the reflector using the function statement object of
     * PHP-Parser.
     *
     * @param \PHPParser_Node_Stmt $node Function object coming from PHP-Parser.
     */
    public function __construct(\PHPParser_Node_Stmt $node)
    {
        parent::__construct($node);

        /** @var \PHPParser_Node_Param $param  */
        foreach ($node->params as $param) {
            $reflector = new FunctionReflector\ArgumentReflector($param);
            $this->arguments[$reflector->getName()] = $reflector;
        }
    }

    /**
     * Returns a list of Argument objects.
     *
     * @return FunctionReflector\ArgumentReflector[]
     */
    public function getArguments()
    {
        return $this->arguments;
    }
}
