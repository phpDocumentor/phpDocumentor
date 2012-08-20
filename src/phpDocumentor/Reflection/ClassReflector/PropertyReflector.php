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

namespace phpDocumentor\Reflection\ClassReflector;
use phpDocumentor\Reflection\BaseReflector;

class PropertyReflector extends BaseReflector
{
    /** @var \PHPParser_Node_Stmt_Property */
    protected $property;

    /** @var \PHPParser_Node_Stmt_PropertyProperty */
    protected $node;

    public function __construct(
        \PHPParser_Node_Stmt_Property $property,
        \PHPParser_Node_Stmt_PropertyProperty $node
    ) {
        parent::__construct($node);
        $this->property = $property;
    }

    public function getName()
    {
        return '$'.parent::getName();
    }

    /**
     * Returns the default value or null if none found.
     *
     * Please note that if the default value is null that this method returns
     * string 'null'.
     *
     * @return null|string
     */
    public function getDefault()
    {
        $result = null;
        if ($this->node->default) {
            $result = $this->getRepresentationOfValue($this->node->default);
        }
        return $result;
    }

    /**
     * Returns whether this method is static.
     *
     * @return bool
     */
    public function isAbstract()
    {
        return $this->property->type & \PHPParser_Node_Stmt_Class::MODIFIER_ABSTRACT;
    }

    /**
     * Returns the visibility for this item.
     *
     * The returned value should match either of the following:
     *
     * * public
     * * protected
     * * private
     *
     * If a method has no visibility set in the class definition this method
     * will return 'public'.
     *
     * @return string
     */
    public function getVisibility()
    {
        if ($this->property->type & \PHPParser_Node_Stmt_Class::MODIFIER_PROTECTED) {
            return 'protected';
        }
        if ($this->property->type & \PHPParser_Node_Stmt_Class::MODIFIER_PRIVATE) {
            return 'private';
        }

        return 'public';
    }

    /**
     * Returns whether this method is static.
     *
     * @return bool
     */
    public function isStatic()
    {
        return $this->property->type & \PHPParser_Node_Stmt_Class::MODIFIER_STATIC;
    }

    /**
     * Returns whether this method is final.
     *
     * @return bool
     */
    public function isFinal()
    {
        return $this->property->type & \PHPParser_Node_Stmt_Class::MODIFIER_FINAL;
    }

    /**
     * Returns the parsed DocBlock.
     *
     * @return \phpDocumentor\Reflection\DocBlock|null
     */
    public function getDocBlock()
    {
        $doc_block = null;
        if ($comment = $this->property->getDocComment()) {
            try {
                $doc_block = new \phpDocumentor\Reflection\DocBlock(
                    (string)$comment,
                    $this->getNamespace(),
                    $this->getNamespaceAliases()
                );
                $doc_block->line_number = $comment->getLine();
            } catch (\Exception $e) {
                $this->log($e->getMessage(), 2);
            }
        }

        \phpDocumentor\Event\Dispatcher::getInstance()->dispatch(
            'reflection.docblock-extraction.post',
            \phpDocumentor\Reflection\Event\PostDocBlockExtractionEvent
            ::createInstance($this)->setDocblock($doc_block)
        );

        return $doc_block;
    }
}
