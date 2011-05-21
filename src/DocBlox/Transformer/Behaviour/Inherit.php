<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category   DocBlox
 * @package    Transformation
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
 * @package    Transformation
 * @subpackage Behaviour
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
class DocBlox_Transformer_Behaviour_Inherit implements
    DocBlox_Transformer_Behaviour_Interface
{
    /** @var DocBlox_Core_Log */
    protected $logger = null;

    /**
     * Sets the logger for this behaviour.
     *
     * @param DocBlox_Core_Log $log
     *
     * @return void
     */
    public function setLogger(DocBlox_Core_Log $log)
    {
        $this->logger = $log;
    }

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
        if ($this->logger) {
            $this->logger->log('Adding path information to each xml "file" tag');
        }

        $xpath = new DOMXPath($xml);

        // get all interfaces that do not extend from anything or whose extend
        // is not featured in this project; these are considered root nodes.
        $result = $xpath->query(
            '/project/file/interface[extends=""]' .
            '|/project/file/interface[not(extends = /project/file/class/full_name)]'
        );

        foreach ($result as $node)
        {
            $this->applyInheritanceToNode(array(), $node, $xpath);
        }

        // get all classes that do not extend from anything or whose extend
        // is not featured in this project; these are considered root nodes.
        $result = $xpath->query(
            '/project/file/class[extends=""]' .
            '|/project/file/class[not(extends = /project/file/class/full_name)]'
        );

        /** @var DOMElement $node */
        foreach ($result as $node)
        {
            $this->applyInheritanceToNode(array(), $node, $xpath);
        }

        var_dump($xml->saveXML());
        //        var_dump($result->length);
        //        var_dump($result->item(0))
        //        for($i=0;$i<$result->length;$i++)
        //            var_dump($result->item($i)->textContent);
        //        var_dump($result->item(102)->textContent);
        //        var_dump($result->item(103)->textContent);
        //        var_dump($result->item(104)->textContent);
        //        var_dump($result->item(105)->textContent);
        die();

        // get all nodes without an 'extend' or where the extend does not exist
        // in the structure

        // TODO: fix me

        return $xml;
    }

    /**
     * Checks whether the super contains any reference to the existing methods
     * or properties.
     *
     * $super is not a real class, it is an aggregation of all methods and
     * properties in the inheritance tree. This is done because a method may
     * override another method which is not in the direct parent but several
     * levels upwards.
     *
     * To deal with the situation above we flatten every found $sub into
     * the $super. We only store the properties and methods since anything
     * else does not override.
     *
     * The structure of $super is:
     * * methods, array containing `$method_name => array` pairs
     *   * class, name of the deepest leaf where this method is encountered
     *   * object, DOMElement of the method declaration in the deepest leaf
     * * properties, array containing `$property_name => array` pairs
     *   * class, name of the deepest leaf where this property is encountered
     *   * object, DOMElement of the property declaration in the deepest leaf
     *
     * @param array      $super
     * @param DOMElement $sub
     * @param DOMXPath   $xpath
     *
     * @see applyInheritance() for a complete set of business rules.
     *
     * @return void
     */
    public function applyInheritanceToNode(array $super, DOMElement $sub,
        DOMXPath $xpath)
    {
        $class_name = $sub->getElementsByTagName('full_name')->item(0)
        ->nodeValue;
        $methods = $sub->getElementsByTagName('method');

        /** @var DOMElement $method */
        foreach ($methods as $method)
        {
            // the name is always the first encountered child element with
            // tag name 'name'
            $method_name = $method->getElementsByTagName('name')->item(0)
            ->nodeValue;
            $docblocks = $method->getElementsByTagName('docblock');

            // if the super does not contain a method with this name; add it
            // and continue.
            if (!isset($super['methods'][$method_name])) {

                // only add it if this method has a docblock; otherwise it is
                // useless
                if ($docblocks->length > 0) {
                    $super['methods'][$method_name] = array(
                        'class' => $class_name,
                        'object' => $method
                    );
                }

                continue;
            }

            // if this method does not yet have a docblock; add one; even
            // though DocBlox throws a warning about a missing DocBlock!
            if ($docblocks->length < 1) {
                $docblock = new DOMElement('docblock');
                $method->appendChild($docblock);
            } else {
                /** @var DOMElement $docblock  */
                $docblock = $docblocks->item(0);
            }

            /** @var DOMElement $super_method_object  */
            $super_method_object = $super['methods'][$method_name]['object'];

            /** @var DOMElement $super_docblock  */
            $super_docblock = $super_method_object
                    ->getElementsByTagName('docblock')->item(0);

            // add the short description if the super docblock has one and
            // the sub docblock doesn't
            if ((($docblock->getElementsByTagName('description')->length < 1)
                 || (!$docblock->getElementsByTagName('description')->item(0)->nodeValue))
                && $super_docblock->getElementsByTagName('description')->length > 0
            ) {
                if ($docblock->getElementsByTagName('description')->length > 0) {
                    $docblock->removeChild(
                        $docblock->getElementsByTagName('description')->item(0)
                    );
                }
                $docblock->appendChild(
                    clone $super_docblock->getElementsByTagName('description')->item(0)
                );
            }

            // add the long description if the super docblock has one and
            // the sub docblock doesn't
            if ((($docblock->getElementsByTagName('long-description')->length < 1)
                 || (!trim($docblock->getElementsByTagName('long-description')->item(0)->nodeValue)))
                && $super_docblock->getElementsByTagName('long-description')->length > 0
            ) {
                if ($docblock->getElementsByTagName('long-description')->length > 0) {
                    $docblock->removeChild(
                        $docblock->getElementsByTagName('long-description')->item(0)
                    );
                }

                $docblock->appendChild(
                    clone $super_docblock->getElementsByTagName('long-description')->item(0)
                );
            }

            /** @var DOMElement $tag */
            foreach ($super_docblock->getElementsByTagName('tag') as $tag) {
                if (in_array($tag->getAttribute('name'), array(
                                                              'param', 'return', 'throw', 'throws', 'version', 'copyright', 'author'))
                ) {
                    // TODO: remove any existing tags but make sure to only remove all existing tags and not newly added

                    // insert new ones.
                    $docblock->appendChild(clone $tag);
                }
            }
        }

        $result = $xpath->query('/project/file/*[extends="' . $class_name . '"]');
        foreach ($result as $node)
        {
            $this->applyInheritanceToNode($super, $node, $xpath);
        }
        //        $properties = $sub->getElementsByTagName('property');
    }
}