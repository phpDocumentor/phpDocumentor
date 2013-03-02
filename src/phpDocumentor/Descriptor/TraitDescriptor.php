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
        $this->getProperties()->clearReferences();
        $this->getMethods()->clearReferences();
    }
}
