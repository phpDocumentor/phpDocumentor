<?php
namespace phpDocumentor\Descriptor;

class FileDescriptor extends DescriptorAbstract implements Interfaces\FileInterface
{
    /** @var string */
    protected $hash;

    /** @var string|null */
    protected $source = null;

    /** @var Collection $namespace_aliases */
    protected $namespace_aliases;

    /** @var Collection $includes */
    protected $includes;

    /** @var Collection $constants */
    protected $constants;

    /** @var Collection $functions */
    protected $functions;

    /** @var Collection $classes */
    protected $classes;

    /** @var Collection $interfaces */
    protected $interfaces;

    /** @var Collection $traits */
    protected $traits;

    /** @var Collection $errors */
    protected $markers;

    /** @var Collection $errors */
    protected $errors;

    public function __construct($hash)
    {
        parent::__construct();

        $this->setHash($hash);
        $this->setNamespaceAliases(new Collection());
        $this->setIncludes(new Collection());

        $this->setConstants(new Collection());
        $this->setFunctions(new Collection());
        $this->setClasses(new Collection());
        $this->setInterfaces(new Collection());
        $this->setTraits(new Collection());

        $this->setMarkers(new Collection());
        $this->setErrors(new Collection());
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

    public function setNamespaceAliases(Collection $namespace_aliases)
    {
        $this->namespace_aliases = $namespace_aliases;
    }

    public function getNamespaceAliases()
    {
        return $this->namespace_aliases;
    }

    public function setIncludes(Collection $includes)
    {
        $this->includes = $includes;
    }

    /**
     * @return Collection
     */
    public function getIncludes()
    {
        return $this->includes;
    }

    public function setConstants(Collection $constants)
    {
        $this->constants = $constants;
    }

    public function getConstants()
    {
        return $this->constants;
    }

    public function setFunctions(Collection $functions)
    {
        $this->functions = $functions;
    }

    public function getFunctions()
    {
        return $this->functions;
    }

    public function setClasses(Collection $classes)
    {
        $this->classes = $classes;
    }

    public function getClasses()
    {
        return $this->classes;
    }

    public function setInterfaces(Collection $interfaces)
    {
        $this->interfaces = $interfaces;
    }

    public function getInterfaces()
    {
        return $this->interfaces;
    }

    public function setTraits(Collection $traits)
    {
        $this->traits = $traits;
    }

    public function getTraits()
    {
        return $this->traits;
    }

    /**
     * @param Collection $markers
     */
    public function setMarkers(Collection $markers)
    {
        $this->markers = $markers;
    }

    /**
     * @return Collection
     */
    public function getMarkers()
    {
        return $this->markers;
    }

    public function setErrors(Collection $errors)
    {
        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
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
        $this->getConstants()->clearReferences();
        $this->getFunctions()->clearReferences();
        $this->getClasses()->clearReferences();
        $this->getInterfaces()->clearReferences();
        $this->getTraits()->clearReferences();
    }
}
