<?php
namespace phpDocumentor\Descriptor;

class ClassDescriptor extends DescriptorAbstract implements Interfaces\ClassInterface
{
    protected $extends;

    /** @var Collection $implements */
    protected $implements;

    protected $abstract = false;
    protected $final = false;

    /** @var Collection $constants */
    protected $constants;

    /** @var Collection $properties */
    protected $properties;

    /** @var Collection $methods */
    protected $methods;

    public function __construct()
    {
        parent::__construct();

        $this->setInterfaces(new Collection());
        $this->setConstants(new Collection());
        $this->setProperties(new Collection());
        $this->setMethods(new Collection());
    }

    public function setParentClass($extends)
    {
        $this->extends = $extends;
    }

    public function getParentClass()
    {
        return $this->extends;
    }

    public function setFinal($final)
    {
        $this->final = $final;
    }

    public function isFinal()
    {
        return $this->final;
    }

    public function setAbstract($abstract)
    {
        $this->abstract = $abstract;
    }

    public function isAbstract()
    {
        return $this->abstract;
    }

    protected function setConstants($constants)
    {
        $this->constants = $constants;
    }

    /**
     * @return Collection
     */
    public function getConstants()
    {
        return $this->constants;
    }

    public function setInterfaces($implements)
    {
        $this->implements = $implements;
    }

    public function getInterfaces()
    {
        return $this->implements;
    }

    public function setMethods($methods)
    {
        $this->methods = $methods;
    }

    public function getMethods()
    {
        return $this->methods;
    }

    public function setProperties($properties)
    {
        $this->properties = $properties;
    }

    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * References to child Descriptors/objects should be assigned a null when the containing object is nulled.
     *
     * In this method should all references to objects be assigned the value null; this will clear the references
     * of child objects from other objects.
     *
     * For example:
     *
     *     A class should NULL its constants, properties and methods as they are contained WITHIN the class and become
     *     orphans if not nulled.
     *
     * @return void
     */
    public function clearReferences()
    {
        $this->getMethods()->clearReferences();
        $this->getProperties()->clearReferences();
        $this->getConstants()->clearReferences();
    }
}
