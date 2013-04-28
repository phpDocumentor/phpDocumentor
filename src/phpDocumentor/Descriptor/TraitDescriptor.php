<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor;

/**
 * Descriptor representing a Trait.
 */
class TraitDescriptor extends DescriptorAbstract implements Interfaces\TraitInterface
{
    /** @var Collection $properties */
    protected $properties;

    /** @var Collection $methods */
    protected $methods;

    /**
     * Initializes the all properties representing a collection with a new Collection object.
     */
    public function __construct()
    {
        parent::__construct();

        $this->setProperties(new Collection());
        $this->setMethods(new Collection());
    }

    /**
     * {@inheritDoc}
     */
    public function setMethods(Collection $methods)
    {
        $this->methods = $methods;
    }

    /**
     * {@inheritDoc}
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * {@inheritDoc}
     */
    public function setProperties(Collection $properties)
    {
        $this->properties = $properties;
    }

    /**
     * {@inheritDoc}
     */
    public function getProperties()
    {
        return $this->properties;
    }

    public function setPackage($package)
    {
        parent::setPackage($package);

        foreach ($this->getProperties() as $property) {
            $property->setPackage($package);
        }

        foreach ($this->getMethods() as $method) {
            $method->setPackage($package);
        }
    }
}
