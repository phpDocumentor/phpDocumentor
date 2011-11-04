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
 * @category   DocBlox
 * @package    Transformer
 * @subpackage Behaviour
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
class DocBlox_Plugin_Core_Transformer_Behaviour_Inherit2 extends
    DocBlox_Transformer_Behaviour_Abstract
{
    /**
     * Apply inheritance of docblock elements to all elements.
     *
     * Apply the inheritance rules from root node to edge leaf; this way the
     * inheritance cascades.
     *
     * Note: the process below must _first_ be done on interfaces and a second
     * pass on classes. If this is not done then not everything will be picked
     * up because you effectively have 2 separate sets of root nodes.
     *
     * This does mean that an interface will populate any class in which it is
     * implemented but will not walk further down the tree.
     *
     * Interfaces do not check whether they implement another interface because
     * interfaces do not support the IMPLEMENTS keyword.
     *
     * Actions:
     *
     * 1. Get root nodes with present leafs
     * 2. Get Extended/implemented leafs
     * 3. If SD misses for leaf; apply SD of root
     * 4. If LD misses for leaf; apply LD of root
     * 5. if LD of leaf contains {@inheritdoc}; replace with LD of root
     * 6. if @category of leaf is missing; use @category of root
     * 7. if @package of leaf is missing; use @package of root
     * 8. if @subcategory of leaf is missing; use @subpackage of root
     * 9. if @version of leaf is missing; use @version of root
     * 10. if @copyright of leaf is missing; use @copyright of root
     * 11. if @author of leaf is missing; use @author of root
     *
     * 12. If root and leaf share a method with the same name:
     * 13. If SD misses for leaf method; apply SD of root method
     * 14. If LD misses for leaf method; apply LD of root method
     * 15. if LD of leaf method contains {@inheritdoc}; replace with LD of root method
     * 16. if @params of leaf method is missing; use @params of root method
     * 17. if @return of leaf method is missing; use @return of root method
     * 18. if @throw/throws of leaf method is missing; use @throws/throw of root method
     * 19. if @version of leaf method is missing; use @version of root method
     * 20. if @copyright of leaf method is missing; use @copyright of root method
     * 21. if @author of leaf method is missing; use @author of root method
     *
     * 22. If root and leaf share a property with the same name:
     * 23. If SD misses for leaf property; apply SD of root property
     * 24. If LD misses for leaf property; apply LD of root property
     * 25. if LD of leaf property contains {@inheritdoc}; replace with LD of root property
     * 26. if @var of leaf property is missing; use @var of root property
     * 27. if @version of leaf property is missing; use @version of root property
     * 28. if @copyright of leaf property is missing; use @copyright of root property
     * 29. if @author of leaf property is missing; use @author of root property
     *
     * @param DOMDocument $xml
     *
     * @return DOMDocument
     */
    public function process(DOMDocument $xml)
    {
        $this->log('Copying all inherited elements');

        $xpath = new DOMXPath($xml);

        $nodes = array();
        echo (memory_get_usage() / 1024 / 1024).'mb' . PHP_EOL;
        /** @var DOMElement[] $result */
        $result = $xpath->query('/project/file/interface|/project/file/class');
//        foreach ($result as $node) {
//            echo '.';
//            $fqcn = $node->getElementsByTagName('full_name')->item(0)->nodeValue;
//            $nodes[$fqcn] = array(
//                'xml_node' => $node,
//                'list_node' => null
//            );
//        }


        $tree = array();
        $nodes = array();

        foreach ($result as $node) {
            $fqcn = $node->getElementsByTagName('full_name')->item(0)->nodeValue;
            $extends = $node->getElementsByTagName('extends')->item(0)->nodeValue;
            $implements = $node->getElementsByTagName('implements');

            $parents = $extends ? array($extends) : array();
            foreach($implements as $implement) {
                $parents[] = (string)$implement->nodeValue;
            }

            // add the node as 'asterisk' (*) as that is not a valid classname
            // and classnames are used as keys
            $nodes[$fqcn] = array('*' => $node);

            if (!$parents) {
                $tree[$fqcn] = &$nodes[$fqcn];
            } else {
                foreach($parents as $extend) {
                    if (!isset($nodes[$extend])) {
                        $nodes[$extend] = array();
                    }
                    $nodes[$extend][$fqcn] = &$nodes[$fqcn];
                    var_dump('1: '.$extend);
                    var_dump('2: ' .$fqcn);
                }
            }
        }

//        print_r($tree);


        echo PHP_EOL;
        echo $result->length.PHP_EOL;
        echo count($nodes).PHP_EOL;
        echo (memory_get_usage() / 1024 / 1024) . 'mb'.PHP_EOL;

        return $xml;
    }
}