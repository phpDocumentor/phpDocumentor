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

class InterfaceReflector extends BaseReflector
{
    /** @var \PHPParser_Node_Stmt_Class */
    protected $node;
    protected $constants = array();
    protected $properties = array();
    protected $methods = array();

    public function __construct(\PHPParser_Node_Stmt $node)
    {
        parent::__construct($node);

        foreach ($this->node->stmts as $stmt) {
            switch(get_class($stmt)) {
                case 'PHPParser_Node_Stmt_Property':
                    foreach ($stmt->props as $property) {
                        $reflector = new ClassReflector\PropertyReflector($stmt, $property);
                        $this->properties[$reflector->getName()] = $reflector;
                    }
                    break;
                case 'PHPParser_Node_Stmt_ClassMethod':
                    $reflector = new ClassReflector\MethodReflector($stmt);
                    $this->methods[$reflector->getName()] = $reflector;
                    break;
                case 'PHPParser_Node_Stmt_ClassConst':
                    foreach ($stmt->consts as $constant) {
                        $reflector = new ClassReflector\ConstantReflector($constant);
                        $this->constants[$reflector->getName()] = $reflector;
                    }
                    break;
            }
        }
    }

    public function getParentClass()
    {
        return $this->node->extends ? (string)$this->node->extends : '';
    }

    /**
     * BC Break: used to be getParentInterfaces
     */
    public function getInterfaces()
    {
        $names = array();
        if ($this->node->implements) {
            /** @var \PHPParser_Node_Name */
            foreach ($this->node->implements as $node) {
                $names[] = (string)$node;
            }
        }
        return $names;
    }

    public function getConstants()
    {
        return $this->constants;
    }

    public function getProperties()
    {
        return $this->properties;
    }

    public function getMethods()
    {
        return $this->methods;
    }

    public function getMethod($name)
    {
        return $this->methods[$name];
    }
}
