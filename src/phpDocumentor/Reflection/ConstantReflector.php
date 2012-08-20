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
 * Provides Static Reflection for file-level constants.
 *
 * @author  Mike van Riel <mike.vanriel@naenius.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    http://phpdoc.org
 */
class ConstantReflector extends BaseReflector
{
    /** @var \PHPParser_Node_Stmt_Const */
    protected $constant;

    /** @var \PHPParser_Node_Const */
    protected $node;

    /**
     * Registers the Constant Statement and Node with this reflector.
     *
     * @param \PHPParser_Node_Stmt_Const $stmt
     * @param \PHPParser_Node_Const      $node
     */
    public function __construct($stmt, $node)
    {
        $this->constant = $stmt;
        parent::__construct($node);
    }

    /**
     * Returns the value contained in this Constant.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->getRepresentationOfValue($this->node->value);
    }

    /**
     * Returns the parsed DocBlock.
     *
     * @return \phpDocumentor\Reflection\DocBlock|null
     */
    public function getDocBlock()
    {
        $doc_block = null;
        $comment = $this->constant->getDocComment();
        if ($comment) {
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
