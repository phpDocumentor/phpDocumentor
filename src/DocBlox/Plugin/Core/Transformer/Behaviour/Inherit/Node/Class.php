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
 * Responsible for adding inheritance behaviour to an individual class.
 *
 * @category   DocBlox
 * @package    Transformer
 * @subpackage Behaviour
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
class DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Class
    extends DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Abstract
{
    /**
     * Determines whether the inheritance for this class has already been processed.
     *
     * This is used for the algorythm that build the inheritance tree; whenever
     * this class is instructed to start inheriting it will first check whether
     * its parent interfaces and classes are processed.
     *
     * If not then those are processed first; by always processing the parents
     * first you get a recursive algorythm where independent of your starting
     * class you always end up with a bottom-to-top population mechanism.
     *
     * A positive side-effect is that no class is processed multiple times;
     * optimizing performance.
     *
     * @var bool
     */
    public $is_processed = false;

    /**
     * Returns the Fully Qualified Class Name for this class or interface.
     *
     * @return string
     */
    function getFQCN()
    {
        return $this->node->getElementsByTagName('full_name')->item(0)->nodeValue;
    }

    /**
     * Returns the name of th super class (if any)
     *
     * @return string
     */
    function getSuperclassName()
    {
        $parent = $this->node->getElementsByTagName('extends');
        return $parent->length > 0 ? $parent->item(0)->nodeValue : '';
    }

    /**
     * Returns the names of the interfaces that are implemented by this class.
     *
     * @return string[]
     */
    function getInterfacesNames()
    {
        $result = array();
        $interfaces = $this->node->getElementsByTagName('implements');

        /** @var $interface DOMElement */
        foreach ($interfaces as $interface) {
            $result[] = $interface->nodeValue;
        }

        return $result;
    }

    /**
     * Returns all child methods.
     *
     * @return DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Method[]
     */
    public function getMethods()
    {
        $result = array();
        $nodes = $this->getDirectElementsByTagName($this->node, 'method');
        foreach ($nodes as $node) {
            $node
                = new DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Method(
                $node, $this->nodes, $this
            );
            $result[$node->getName()] = $node;
        }

        return $result;
    }

    /**
     * Returns all child properties.
     *
     * @return DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Property
     */
    public function getProperties()
    {
        $result = array();
        $nodes = $this->getDirectElementsByTagName($this->node, 'property');
        foreach ($nodes as $node) {
            $node
                = new DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Property(
                $node, $this->nodes, $this
            );
            $result[$node->getName()] = $node;
        }

        return $result;
    }

    /**
     * Returns all class constants.
     *
     * @return DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Constant
     */
    public function getConstants()
    {
        $result = array();
        $nodes = $this->getDirectElementsByTagName($this->node, 'constant');
        foreach ($nodes as $node) {
            $node
                = new DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Constant(
                $node, $this->nodes, $this
            );
            $result[$node->getName()] = $node;
        }

        return $result;
    }

    /**
     * Inherits all methods from the given parent class.
     *
     * @param DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Class $parent
     *
     * @return void
     */
    protected function inheritMethods(
        DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Class $parent
    ) {
        /** @var DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Method[] $methods */
        $methods = $this->getMethods();

        /** @var DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Method $parent_method */
        foreach ($parent->getMethods() as $key => $parent_method) {
            if (isset($methods[$key])) {
                $methods[$key]->inherit($parent_method);
            } else {
                $parent_method->copyTo($this);
            }
        }
    }

    /**
     * Inherits all properties from a given base class.
     *
     * @param DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Class $parent
     *
     * @return void
     */
    protected function inheritProperties(
        DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Class $parent
    ) {
        $properties = $this->getProperties();

        foreach ($parent->getProperties() as $key => $parent_property) {
            if (isset($properties[$key])) {
                $properties[$key]->inherit($parent_property);
            } else {
                $parent_property->copyTo($this);
            }
        }
    }

    /**
     * Inherits all constants from a given base class.
     *
     * @param DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Class $parent
     *
     * @return void
     */
    protected function inheritConstants(
        DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Class $parent
    ) {
        /** @var DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Constant[] $constants */
        $constants = $this->getConstants();

        /** @var DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Constant $parent_constant */
        foreach ($parent->getConstants() as $key => $parent_constant) {
            if (isset($constants[$key])) {
                $constants[$key]->inherit($parent_constant);
            } else {
                $parent_constant->copyTo($this);
            }
        }
    }

    /**
     * Inherits the given parent as if it was an interface.
     *
     * @param DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Class $parent
     *
     * @todo this method and inheritClass should be separated into different objects
     *
     * @return void
     */
    protected function inheritInterfaceObject(
        DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Class $parent
    ) {
        // if the implemented interface has not processed yet; do so. This will
        // cause a recurring effect which makes sure the tree is traversed
        // bottom-to-top
        if (!$parent->is_processed) {
            $parent->inherit(null);
        }

        $this->inheritConstants($parent);
        $this->inheritMethods($parent);
    }

    /**
     * Inherits the given parent as if it was an class.
     *
     * @param DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Class $parent
     *
     * @todo this method and inheritInterface should be separated into
     *     different objects
     *
     * @return void
     */
    protected function inheritClassObject(
        DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Class $parent
    ) {
        // if the parent class has not processed yet; do so. This will cause
        // a recurring effect which makes sure the tree is traversed bottom-to-top
        if (!$parent->is_processed) {
            $parent->inherit(null);
        }

        $docblock = $this->getDocBlock();
        $docblock->inherited_tags[] = 'package';
        $docblock->inherited_tags[] = 'subpackage';
        $docblock->inherit($parent->getDocBlock());

        $this->inheritConstants($parent);
        $this->inheritProperties($parent);
        $this->inheritMethods($parent);
    }

    /**
     * Imports a method that is obtained via reflection.
     *
     * @param ReflectionMethod $method
     *
     * @see self::reflectInternalClass for a complete description.
     *
     * @return DOMElement|null
     */
    protected function importReflectedMethod(ReflectionMethod $method)
    {
        if ($method->isPrivate()) {
            return null;
        }

        $class_name = $method->getDeclaringClass()->getName();
        $methods = $this->getMethods();
        if (in_array($method->getName(), array_keys($methods))) {
            $methods[$method->getName()]->getNode()->appendChild(
                new DOMElement('overrides-from', $class_name)
            );

            return $methods[$method->getName()]->getNode();
        }

        $method_node = new DOMElement('method');
        $this->node->appendChild($method_node);

        $node_name = new DOMElement('name', $method->getName());
        $method_node->appendChild($node_name);
        $method_node->setAttribute(
            'final', $method->isFinal() ? 'true' : 'false'
        );
        $method_node->setAttribute(
            'abstract', $method->isAbstract() ? 'true' : 'false'
        );
        $method_node->setAttribute(
            'static', $method->isStatic() ? 'true' : 'false'
        );
        $method_node->setAttribute(
            'visibility', $method->isPublic() ? 'public' : 'protected'
        );

        $method_obj = new DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Method($method_node, $this->nodes, $this);

        $inherited_from = new DOMElement('tag');
        $method_obj->getDocBlock()->getNode()->appendChild($inherited_from);
        $inherited_from->setAttribute('name', 'inherited_from');
        $inherited_from->setAttribute(
            'refers',
            $method->getDeclaringClass()->getName() . '::' . $method->getName() . '()'
        );
        $inherited_from->setAttribute(
            'description',
            $method->getDeclaringClass()->getName() . '::' . $method->getName() . '()'
        );

        return $method_node;
    }

    /**
     * Reflect an external class and inherit its children.
     *
     * This method is used when the parent class is not any of the files that
     * were parsed by DocBlox but is obtainable in the path. For these files
     * we want to import their methods so that the overview is complete.
     *
     * Examples of such classes are classes that are in PHP Core (i.e. Exception)
     * or available via PECL extensions.
     *
     * @todo consider moving this to a separate object?
     *
     * @param string $parent_class_name
     *
     * @return void
     */
    protected function reflectExternalClass($parent_class_name)
    {
        if (@class_exists($parent_class_name)) {
            $refl = new ReflectionClass($parent_class_name);

            /** @var ReflectionMethod $method */
            foreach ($refl->getMethods() as $method) {
                $this->importReflectedMethod($method);
            }
        }
    }

    /**
     * Traverse through each parent interface and class and inherit its children.
     *
     * @param $parent nil; is not used in this method. Only there because it is
     *     required by the parent class.
     *
     * @return void
     */
    public function inherit($parent)
    {
        foreach ($this->getInterfacesNames() as $interface) {
            if (isset($this->nodes[$interface])) {
                $this->inheritInterfaceObject($this->nodes[$interface]);
            }
        }

        if ($this->getSuperclassName()) {
            if (!isset($this->nodes[$this->getSuperclassName()])) {
                $this->reflectExternalClass($this->getSuperclassName());
            } else {
                $this->inheritClassObject($this->nodes[$this->getSuperclassName()]);
            }
        }

        $this->is_processed = true;
    }

}