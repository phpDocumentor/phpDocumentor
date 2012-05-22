<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @category   phpDocumentor
 * @package    Transformer
 * @subpackage Behaviour
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Core\Transformer\Behaviour;

/**
 * Behaviour that runs through all elements and inherit base information
 * when necessary.
 *
 * Each class or interface needs to be examined from bottom to top. Since classes
 * can inherit properties, methods and constants from multiple parents (both
 * classes and interfaces) it is necessary to track whether all parents have
 * been processed before processing a class.
 *
 * If a parent class and interface both have the same method declared; inherit
 * the class' method as that will probably contain more specific information.
 *
 * @category   phpDocumentor
 * @package    Transformer
 * @subpackage Behaviour
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */
class Inherit extends \phpDocumentor\Transformer\Behaviour\BehaviourAbstract
{
    /**
     * Apply inheritance of docblock elements to all elements.
     *
     * Apply the inheritance rules from root node to edge leaf; this way the
     * inheritance cascades.
     *
     * @param \DOMDocument $xml XML structure to apply the behaviour on.
     *
     * @return \DOMDocument
     */
    public function process(\DOMDocument $xml)
    {
        $this->log('Copying all inherited elements');

        $xpath = new \DOMXPath($xml);

        /** @var DOMElement[] $result */
        $result = $xpath->query('/project/file/interface|/project/file/class');

        $nodes = array();
        foreach ($result as $node) {
            $class = new Inherit\Node\ClassNode($node, $nodes);

            $nodes[$class->getFQCN()] = $class;
        }

        /** @var Inherit\Node\ClassNode $node */
        foreach ($nodes as $node) {
            $node->setNodes($nodes);

            // cascading is done within the $node; see the DocBlock of 'inherit'
            // to get a picture of how this works.
            $node->inherit(null);
        }

        return $xml;
    }
}