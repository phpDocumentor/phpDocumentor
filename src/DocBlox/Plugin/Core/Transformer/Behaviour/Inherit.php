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
class DocBlox_Plugin_Core_Transformer_Behaviour_Inherit extends
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

        // get all interfaces that do not extend from anything or whose extend
        // is not featured in this project; these are considered root nodes.
        /** @var DOMElement[] $result */
        $result = $xpath->query(
            '/project/file/interface[extends=""]' .
            '|/project/file/interface[not(extends = /project/file/class/full_name)]'
        );
        foreach ($result as $node)
        {
            $inherit = new DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Interface(
                $node, $xml
            );
            $super = array('classes' => array(), 'properties' => array(), 'methods' => array());
            $inherit->apply($super, null);
        }

        // get all classes that do not extend from anything or whose extend
        // is not featured in this project; these are considered root nodes.
        /** @var DOMElement[] $result */
        $result = $xpath->query(
            '/project/file/class[extends=""]' .
            '|/project/file/class[not(extends = /project/file/class/full_name)]'
        );
        foreach ($result as $node)
        {
            $inherit = new DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Class(
                $node, $xml
            );

            $methods = array();

            // shut up operator is necessary to silence autoloaders
            $parent_class_name = $node->getElementsByTagName('extends')
                ->item(0)->nodeValue;
            if (@class_exists($parent_class_name)) {
                $refl = new ReflectionClass($parent_class_name);

                /** @var ReflectionMethod $method */
                foreach($refl->getMethods() as $method) {
                    if ($method->isPrivate()) {
                        continue;
                    }

                    $node_name = new DOMElement('name', $method->getName());
                    $method_node = $xml->createElement('method');
                    $method_node->appendChild($node_name);
                    $method_node->setAttribute('final', $method->isFinal() ? 'true' : 'false');
                    $method_node->setAttribute('abstract', $method->isAbstract() ? 'true' : 'false');
                    $method_node->setAttribute('static', $method->isStatic() ? 'true' : 'false');
                    $method_node->setAttribute(
                        'visibility',
                        $method->isPublic() ? 'public' : 'protected'
                    );

                    $methods[$method->getName()] = array(
                        'class'  => $parent_class_name,
                        'object' => $method_node
                    );
                }
            }

            $super = array(
                'classes' => array(),
                'properties' => array(),
                'methods' => $methods
            );

            $inherit->apply($super, null);
        }

        return $xml;
    }
}