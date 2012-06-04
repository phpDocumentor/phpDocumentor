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

class Traverser
{
    public $visitor;

    public function traverse($contents)
    {
        $parser = new \PHPParser_Parser(new \PHPParser_Lexer());
        $node_traverser = new \PHPParser_NodeTraverser();
        $node_traverser->addVisitor(new \PHPParser_NodeVisitor_NameResolver());
        $node_traverser->addVisitor($this->visitor);

        try {
            $stmts = $parser->parse($contents);
            $node_traverser->traverse($stmts);

        } catch (\PHPParser_Error $e) {
            echo 'Parse Error: ', $e->getMessage();
        }
    }
}
