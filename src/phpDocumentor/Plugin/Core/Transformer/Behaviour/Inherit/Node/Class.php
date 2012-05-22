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

namespace phpDocumentor\Plugin\Core\Transformer\Behaviour\Inherit\Node;

/**
 * Responsible for adding inheritance behaviour to an individual class.
 *
 * @category   phpDocumentor
 * @package    Transformer
 * @subpackage Behaviour
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */
class ClassNode extends NodeAbstract
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

        /** @var $interface \DOMElement */
        foreach ($interfaces as $interface) {
            $result[] = $interface->nodeValue;
        }

        return $result;
    }

    /**
     * Returns all child methods.
     *
     * @return MethodNode[]
     */
    public function getMethods()
    {
        $result = array();
        $nodes = $this->getDirectElementsByTagName($this->node, 'method');
        foreach ($nodes as $node) {
            $node = new MethodNode($node, $this->nodes, $this);
            $result[$node->getName()] = $node;
        }

        return $result;
    }

    /**
     * Returns all child properties.
     *
     * @return PropertyNode[]
     */
    public function getProperties()
    {
        $result = array();
        $nodes = $this->getDirectElementsByTagName($this->node, 'property');
        foreach ($nodes as $node) {
            $node = new PropertyNode($node, $this->nodes, $this);
            $result[$node->getName()] = $node;
        }

        return $result;
    }

    /**
     * Returns all class constants.
     *
     * @return ConstantNode[]
     */
    public function getConstants()
    {
        $result = array();
        $nodes = $this->getDirectElementsByTagName($this->node, 'constant');
        foreach ($nodes as $node) {
            $node = new ConstantNode($node, $this->nodes, $this);
            $result[$node->getName()] = $node;
        }

        return $result;
    }

    /**
     * Inherits all methods from the given parent class.
     *
     * @param ClassNode $parent parent object to inherit methods from.
     *
     * @return void
     */
    protected function inheritMethods(ClassNode $parent)
    {
        /** @var MethodNode[] $methods */
        $methods = $this->getMethods();

        /** @var MethodNode $parent_method */
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
     * @param ClassNode $parent parent object to inherit properties from.
     *
     * @return void
     */
    protected function inheritProperties(ClassNode $parent)
    {
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
     * @param ClassNode $parent parent object to inherit constants from.
     *
     * @return void
     */
    protected function inheritConstants(ClassNode $parent)
    {
        /** @var ConstantNode[] $constants */
        $constants = $this->getConstants();

        /** @var ConstantNode $parent_constant */
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
     * @param ClassNode $parent Parent interface to inherit from.
     *
     * @todo this method and inheritClass should be separated into different objects
     *
     * @return void
     */
    protected function inheritInterfaceObject(ClassNode $parent)
    {
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
     * @param ClassNode $parent Parent class to inherit from.
     *
     * @todo this method and inheritInterface should be separated into
     *     different objects
     *
     * @return void
     */
    protected function inheritClassObject(ClassNode $parent)
    {
        // if the parent class has not processed yet; do so. This will cause
        // a recurring effect which makes sure the tree is traversed bottom-to-top
        if (!$parent->is_processed) {
            $parent->setNodes($this->nodes);
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
     * @param \ReflectionMethod $method Method that is to be imported.
     *
     * @see self::reflectInternalClass for a complete description.
     *
     * @return \DOMElement|null
     */
    protected function importReflectedMethod(\ReflectionMethod $method)
    {
        if ($method->isPrivate()) {
            return null;
        }

        $class_name = $method->getDeclaringClass()->getName();
        $methods = $this->getMethods();
        if (in_array($method->getName(), array_keys($methods))) {
            $methods[$method->getName()]->getNode()->appendChild(
                new \DOMElement('overrides-from', $class_name)
            );

            return $methods[$method->getName()]->getNode();
        }

        $method_node = new \DOMElement('method');
        $this->node->appendChild($method_node);

        $node_name = new \DOMElement('name', $method->getName());
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

        $method_obj = new MethodNode($method_node, $this->nodes, $this);

        $inherited_from = new \DOMElement('tag');
        $method_obj->getDocBlock()->getNode()->appendChild($inherited_from);
        $inherited_from->setAttribute('name', 'inherited_from');
        $inherited_from->setAttribute(
            'refers',
            $method->getDeclaringClass()->getName() . '::'
            . $method->getName() . '()'
        );
        $inherited_from->setAttribute(
            'description',
            $method->getDeclaringClass()->getName() . '::'
            . $method->getName() . '()'
        );

        return $method_node;
    }

    /**
     * Reflect an external class and inherit its children.
     *
     * This method is used when the parent class is not any of the files that
     * were parsed by phpDocumentor but is obtainable in the path. For these files
     * we want to import their methods so that the overview is complete.
     *
     * Examples of such classes are classes that are in PHP Core (i.e. Exception)
     * or available via PECL extensions.
     *
     * @param string $parent_class_name FQCL of the external class.
     *
     * @todo consider moving this to a separate object?
     *
     * @return void
     */
    protected function reflectExternalClass($parent_class_name)
    {
        if (@class_exists($parent_class_name)) {
            $refl = new \ReflectionClass($parent_class_name);

            /** @var \ReflectionMethod $method */
            foreach ($refl->getMethods() as $method) {
                $this->importReflectedMethod($method);
            }
        }
    }

    /**
     * Traverse through each parent interface and class and inherit its children.
     *
     * @param null $parent is not used in this method. Only there because it is
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