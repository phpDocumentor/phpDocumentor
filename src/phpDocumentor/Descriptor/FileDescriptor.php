<?php
namespace phpDocumentor\Descriptor;

class FileDescriptor extends DescriptorAbstract
{
    /** @var string */
    protected $hash;

    /** @var string|null */
    protected $source = null;

    /** @var \ArrayObject $namespace_aliases */
    protected $namespace_aliases;

    /** @var \ArrayObject $includes */
    protected $includes;

    /** @var \ArrayObject $constants */
    protected $constants;

    /** @var \ArrayObject $functions */
    protected $functions;

    /** @var \ArrayObject $classes */
    protected $classes;

    /** @var \ArrayObject $interfaces */
    protected $interfaces;

    /** @var \ArrayObject $traits */
    protected $traits;

    /** @var \ArrayObject $errors */
    protected $errors;

    public function __construct($hash)
    {
        parent::__construct();

        $this->setHash($hash);
        $this->setNamespaceAliases(new \ArrayObject());
        $this->setIncludes(new \ArrayObject());

        $this->setConstants(new \ArrayObject());
        $this->setFunctions(new \ArrayObject());
        $this->setClasses(new \ArrayObject());
        $this->setInterfaces(new \ArrayObject());
        $this->setTraits(new \ArrayObject());

        $this->setErrors(new \ArrayObject());
    }

    protected function setHash($hash)
    {
        $this->hash = $hash;
    }

    public function getHash()
    {
        return $this->hash;
    }

    public function setSource($source)
    {
        $this->source = $source;
    }

    public function getSource()
    {
        return $this->source;
    }

    protected function setNamespaceAliases(\ArrayObject $namespace_aliases)
    {
        $this->namespace_aliases = $namespace_aliases;
    }

    public function getNamespaceAliases()
    {
        return $this->namespace_aliases;
    }

    protected function setIncludes(\ArrayObject $includes)
    {
        $this->includes = $includes;
    }

    /**
     * @return \ArrayObject
     */
    public function getIncludes()
    {
        return $this->includes;
    }

    protected function setConstants(\ArrayObject $constants)
    {
        $this->constants = $constants;
    }

    public function getConstants()
    {
        return $this->constants;
    }

    protected function setFunctions(\ArrayObject $functions)
    {
        $this->functions = $functions;
    }

    public function getFunctions()
    {
        return $this->functions;
    }

    protected function setClasses(\ArrayObject $classes)
    {
        $this->classes = $classes;
    }

    public function getClasses()
    {
        return $this->classes;
    }

    protected function setInterfaces(\ArrayObject $interfaces)
    {
        $this->interfaces = $interfaces;
    }

    public function getInterfaces()
    {
        return $this->interfaces;
    }

    protected function setTraits(\ArrayObject $traits)
    {
        $this->traits = $traits;
    }

    public function getTraits()
    {
        return $this->traits;
    }

    protected function setErrors(\ArrayObject $errors)
    {
        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
