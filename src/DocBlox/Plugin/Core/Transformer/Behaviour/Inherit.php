<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category   DocBlox
 * @package    Transformer
 * @subpackage Behaviour
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */

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
 * @category   DocBlox
 * @package    Transformer
 * @subpackage Behaviour
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
class DocBlox_Plugin_Core_Transformer_Behaviour_Inherit extends
    DocBlox_Transformer_Behaviour_Abstract
{
    /**
     * Apply inheritance of docblock elements to all elements.
     *
     * Apply the inheritance rules from root node to edge leaf; this way the
     * inheritance cascades.
     *
     * @param DOMDocument $xml
     *
     * @return DOMDocument
     */
    public function process(DOMDocument $xml)
    {
        $this->log('Copying all inherited elements');

        $xpath = new DOMXPath($xml);

        /** @var DOMElement[] $result */
        $result = $xpath->query('/project/file/interface|/project/file/class');

        $nodes = array();
        foreach ($result as $node) {
            $class
                = new DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Class(
                $node, $nodes
            );

            $nodes[$class->getFQCN()] = $class;
        }

        /** @var DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Class $node */
        foreach($nodes as $node) {
            // cascading is done within the $node; see the DocBlock of 'inherit'
            // to get a picture of how this works.
            $node->inherit(null);
        }

        return $xml;
    }
}