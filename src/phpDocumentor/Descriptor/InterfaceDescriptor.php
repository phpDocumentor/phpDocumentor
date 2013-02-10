<?php
namespace phpDocumentor\Descriptor;

class InterfaceDescriptor extends DescriptorAbstract implements Interfaces\InterfaceInterface
{
    /** @var Collection $extends */
    protected $extends;

    /** @var Collection $methods */
    protected $methods;

    public function __construct()
    {
        parent::__construct();

        $this->setParentInterfaces(new Collection());
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
}
