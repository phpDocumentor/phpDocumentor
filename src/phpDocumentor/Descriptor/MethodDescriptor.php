<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor;

/**
 * Descriptor representing a Method in a Class, Interface or Trait.
 */
class MethodDescriptor extends DescriptorAbstract implements Interfaces\MethodInterface, Interfaces\VisibilityInterface
{
    /** @var ClassDescriptor|InterfaceDescriptor|TraitDescriptor $parent */
    protected $parent;

    /** @var bool $abstract */
    protected $abstract = false;

    /** @var bool $final */
    protected $final = false;

    /** @var bool $static */
    protected $static = false;

    /** @var string $visibility */
    protected $visibility = 'public';

    /** @var Collection */
    protected $arguments;

    /**
     * Initializes the all properties representing a collection with a new Collection object.
     */
    public function __construct()
    {
        parent::__construct();

        $this->setArguments(new Collection());
    }

    /**
     * @param ClassDescriptor|InterfaceDescriptor|TraitDescriptor $parent
     */
    public function setParent($parent)
    {
        $this->setFullyQualifiedStructuralElementName(
            $parent->getFullyQualifiedStructuralElementName() . '::' . $this->getName() . '()'
        );

        // reset cached inherited element so that it can be re-detected.
        $this->inheritedElement = null;

        $this->parent = $parent;
    }

    /**
     * @return ClassDescriptor|InterfaceDescriptor|TraitDescriptor
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * {@inheritDoc}
     */
    public function setAbstract($abstract)
    {
        $this->abstract = $abstract;
    }

    /**
     * {@inheritDoc}
     */
    public function isAbstract()
    {
        return $this->abstract;
    }

    /**
     * {@inheritDoc}
     */
    public function setFinal($final)
    {
        $this->final = $final;
    }

    /**
     * {@inheritDoc}
     */
    public function isFinal()
    {
        return $this->final;
    }

    /**
     * {@inheritDoc}
     */
    public function setStatic($static)
    {
        $this->static = $static;
    }

    /**
     * {@inheritDoc}
     */
    public function isStatic()
    {
        return $this->static;
    }

    /**
     * {@inheritDoc}
     */
    public function setVisibility($visibility)
    {
        $this->visibility = $visibility;
    }

    /**
     * {@inheritDoc}
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * {@inheritDoc}
     */
    public function setArguments(Collection $arguments)
    {
        foreach( $arguments as $argument) {
            $argument->setMethod($this);
        }
        $this->arguments = $arguments;
    }

    /**
     * @param string $name
     * @param ArgumentDescriptor $argument
     */
    public function addArgument($name, ArgumentDescriptor $argument)
    {
        $argument->setMethod($this);
        $this->arguments->set($name, $argument);
    }

    /**
     * {@inheritDoc}
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * {@inheritDoc}
     */
    public function getResponse()
    {
        /** @var Collection|null $returnTags */
        $returnTags = $this->getReturn();

        return $returnTags instanceof Collection && $returnTags->count() > 0
            ? current($returnTags->getAll())
            : null;
    }

    /**
     * Returns the file associated with the parent class, interface or trait.
     *
     * @return FileDescriptor
     */
    public function getFile()
    {
        return $this->getParent()->getFile();
    }

    /**
     * @return Collection
     */
    public function getReturn()
    {
        /** @var Collection $var */
        $var = $this->getTags()->get('return', new Collection());
        if ($var->count() != 0) {
            return $var;
        }

        $inheritedElement = $this->getInheritedElement();
        if ($inheritedElement) {
            return $inheritedElement->getReturn();
        }

        return new Collection();
    }

    /**
     * @return Collection
     */
    public function getParam()
    {
        /** @var Collection $var */
        $var = $this->getTags()->get('param', new Collection());
        if ($var instanceof Collection && $var->count() > 0) {
            return $var;
        }

        $inheritedElement = $this->getInheritedElement();
        if ($inheritedElement) {
            return $inheritedElement->getParam();
        }

        return new Collection();
    }

    /**
     * Returns the Method from which this method should inherit its information, if any.
     *
     * The inheritance scheme for a method is more complicated than for most elements; the following business rules
     * apply:
     *
     * 1. if the parent class/interface extends another class or other interfaces (interfaces have multiple
     *    inheritance!) then:
     *    1. Check each parent class/interface's parent if they have a method with the exact same name
     *    2. if a method is found with the same name; return the first one encountered.
     * 2. if the parent is a class and implements interfaces, check each interface for a method with the exact same
     *    name. If such a method is found, return the first hit.
     *
     * @return MethodDescriptor|null
     */
    public function getInheritedElement()
    {
        if ($this->inheritedElement !== null) {
            return $this->inheritedElement;
        }

        /** @var ClassDescriptor|InterfaceDescriptor|null $associatedClass */
        $associatedClass = $this->getParent();
        if (!$associatedClass instanceof ClassDescriptor && !$associatedClass instanceof InterfaceDescriptor) {
            return null;
        }

        /** @var ClassDescriptor|InterfaceDescriptor $parentClass|null */
        $parentClass = $associatedClass->getParent();
        if ($parentClass) {
            if (!$parentClass instanceof ClassDescriptor && !$parentClass instanceof Collection) {
                return null;
            }

            // the parent of a class is always a class, but the parent of an interface is a collection of interfaces.
            $parents = $parentClass instanceof ClassDescriptor ? array($parentClass) : $parentClass->getAll();
            foreach ($parents as $parent) {
                if ($parent instanceof ClassDescriptor || $parent instanceof InterfaceDescriptor) {
                    $parentMethod = $parent->getMethods()->get($this->getName());
                    if ($parentMethod) {
                        $this->inheritedElement = $parentMethod;

                        return $this->inheritedElement;
                    }
                }
            }
        }

        // also check all implemented interfaces next if the parent is a class and not an interface
        if ($associatedClass instanceof ClassDescriptor) {
            /** @var InterfaceDescriptor $interface */
            foreach ($associatedClass->getInterfaces() as $interface) {
                if (!$interface instanceof InterfaceDescriptor) {
                    continue;
                }

                $parentMethod = $interface->getMethods()->get($this->getName());
                if ($parentMethod) {
                    $this->inheritedElement = $parentMethod;

                    return $this->inheritedElement;
                }
            }
        }

        return null;
    }
}
