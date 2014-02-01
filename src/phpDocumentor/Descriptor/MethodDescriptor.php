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
class MethodDescriptor extends DescriptorAbstract implements Interfaces\MethodInterface
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
     * Returns the summary which describes this element.
     *
     * This method will automatically attempt to inherit the parent's summary if this one has none.
     *
     * @return string
     */
    public function getSummary()
    {
        if ($this->summary && strtolower(trim($this->summary)) != '{@inheritdoc}') {
            return parent::getSummary();
        }

        $parentConstant = $this->getInheritedElement();
        if ($parentConstant) {
            return $parentConstant->getSummary();
        }

        return '';
    }

    /**
     * Returns the description which describes this element.
     *
     * This method will automatically attempt to inherit the parent's description if this one has none.
     *
     * @return string
     */
    public function getDescription()
    {
        if ($this->description && strtolower(trim($this->description)) != '{@inheritdoc}') {
            return parent::getDescription();
        }

        $parentConstant = $this->getInheritedElement();
        if ($parentConstant) {
            return $parentConstant->getDescription();
        }

        return '';
    }

    /**
     * @param ClassDescriptor|InterfaceDescriptor|TraitDescriptor $parent
     */
    public function setParent($parent)
    {
        $this->setFullyQualifiedStructuralElementName(
            $parent->getFullyQualifiedStructuralElementName() . '::' . $this->getName() . '()'
        );

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
        $this->arguments = $arguments;
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
        $returnTags = $this->getTags()->get('return');

        return $returnTags instanceof Collection ? current($returnTags->getAll()) : null;
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
     * Returns the Method from which this one should inherit, if any.
     *
     * @return MethodDescriptor|null
     */
    protected function getInheritedElement()
    {
        /** @var ClassDescriptor|InterfaceDescriptor|null $associatedClass */
        $associatedClass = $this->getParent();

        if (($associatedClass instanceof ClassDescriptor || $associatedClass instanceof InterfaceDescriptor)
            && ($associatedClass->getParent() instanceof ClassDescriptor
                || $associatedClass->getParent() instanceof InterfaceDescriptor
            )
        ) {
            /** @var ClassDescriptor|InterfaceDescriptor $parentClass */
            $parentClass = $associatedClass->getParent();
            return $parentClass->getMethods()->get($this->getName());
        }

        return null;
    }
}
