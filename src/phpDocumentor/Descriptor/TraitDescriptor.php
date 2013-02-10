<?php
namespace phpDocumentor\Descriptor;

class TraitDescriptor extends DescriptorAbstract implements Interfaces\TraitInterface
{
    /** @var Collection $properties */
    protected $properties;

    /** @var Collection $methods */
    protected $methods;

    public function __construct()
    {
        parent::__construct();

        $this->setProperties(new Collection());
        $this->setMethods(new Collection());
    }

    /**
     * @param Collection $methods
     */
    protected function setMethods(Collection $methods)
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
     * @param Collection $properties
     */
    protected function setProperties($properties)
    {
        $this->properties = $properties;
    }

    /**
     * @return Collection
     */
    public function getProperties()
    {
        return $this->properties;
    }
}
