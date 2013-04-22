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
 * Descriptor representing an Interface.
 */
class InterfaceDescriptor extends DescriptorAbstract implements Interfaces\InterfaceInterface
{
    /** @var Collection $extends */
    protected $extends;

    /** @var Collection $constants */
    protected $constants;

    /** @var Collection $methods */
    protected $methods;

    /**
     * Initializes the all properties representing a collection with a new Collection object.
     */
    public function __construct()
    {
        parent::__construct();

        $this->setParent(new Collection());
        $this->setConstants(new Collection());
        $this->setMethods(new Collection());
    }

    /**
     * {@inheritDoc}
     */
    public function setParent(Collection $extends)
    {
        $this->extends = $extends;
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return $this->extends;
    }

    /**
     * {@inheritDoc}
     */
    public function setConstants(Collection $constants)
    {
        $this->constants = $constants;
    }

    /**
     * {@inheritDoc}
     */
    public function getConstants()
    {
        return $this->constants;
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

    public function setPackage($package)
    {
        parent::setPackage($package);

        foreach ($this->getConstants() as $constant) {
            $constant->setPackage($package);
        }

        foreach ($this->getMethods() as $method) {
            $method->setPackage($package);
        }
    }
}
