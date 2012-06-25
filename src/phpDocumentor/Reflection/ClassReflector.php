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

/**
 * Provides static reflection for a class.
 *
 * @author  Mike van Riel <mike.vanriel@naenius.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    http://phpdoc.org
 */
class ClassReflector extends InterfaceReflector
{
    /** @var \PHPParser_Node_Stmt_Class */
    protected $node;

    /** @var string[] */
    protected $traits = array();

    /**
     * Interprets the PHP-Parser statement and constructs a class reflection.
     *
     * @param \PHPParser_Node_Stmt_Class $node A Class node as returned by the
     *     PHP-Parser component.
     */
    public function __construct(\PHPParser_Node_Stmt $node)
    {
        parent::__construct($node);

        /** @var \PHPParser_Node_Stmt_TraitUse $stmt  */
        foreach ($node->stmts as $stmt) {
            if ($stmt instanceof \PHPParser_Node_Stmt_TraitUse) {
                foreach ($stmt->traits as $trait) {
                    $this->traits[] = (string)$trait;
                }
            }
        }
    }

    /**
     * Returns whether this is an abstract class.
     *
     * @return bool
     */
    public function isAbstract()
    {
        return (bool)$this->node->type
            & \PHPParser_Node_Stmt_Class::MODIFIER_ABSTRACT;
    }

    /**
     * Returns whether this class is final and thus cannot be extended.
     *
     * @return bool
     */
    public function isFinal()
    {
        return (bool)$this->node->type
            & \PHPParser_Node_Stmt_Class::MODIFIER_FINAL;
    }

    /**
     * Returns a list of the names of traits used in this class.
     *
     * @return string[]
     */
    public function getTraits()
    {
        return $this->traits;
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
}
