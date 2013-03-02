<?php
namespace phpDocumentor\Descriptor;

class InterfaceDescriptor extends DescriptorAbstract implements Interfaces\InterfaceInterface
{
    /** @var Collection $extends */
    protected $extends;

    /** @var Collection $constants */
    protected $constants;

    /** @var Collection $methods */
    protected $methods;

    public function __construct()
    {
        parent::__construct();

        $this->setParentInterfaces(new Collection());
        $this->setConstants(new Collection());
        $this->setMethods(new Collection());
    }

    /**
     * @param Collection $extends
     */
    public function setParentInterfaces(Collection $extends)
    {
        $this->extends = $extends;
    }

    /**
     * @return Collection
     */
    public function getParentInterfaces()
    {
        return $this->extends;
    }

    /**
     * @param \phpDocumentor\Descriptor\Collection $constants
     */
    public function setConstants($constants)
    {
        $this->constants = $constants;
    }

    /**
     * @return \phpDocumentor\Descriptor\Collection
     */
    public function getConstants()
    {
        return $this->constants;
    }

    /**
     * @param Collection $methods
     */
    public function setMethods(Collection $methods)
    {
        $this->methods = $methods;
    }

    /**
     * @return Collection
     */
    public function getMethods()
    {
        return $this->methods;
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
        $this->getConstants()->clearReferences();
    }
}
