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
 * Represents a file in the project.
 */
class FileDescriptor extends DescriptorAbstract implements Interfaces\FileInterface
{
    /** @var string $hash */
    protected $hash;

    /** @var string $path */
    protected $path = '';

    /** @var string|null $source */
    protected $source = null;

    /** @var Collection $namespaceAliases */
    protected $namespaceAliases;

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

    /** @var Collection $markers */
    protected $markers;

    /**
     * Initializes a new file descriptor with the given hash of its contents.
     *
     * @param string $hash An MD5 hash of the contents if this file.
     */
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
    }

    /**
     * Returns the hash of the contents for this file.
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Sets the hash of the contents for this file.
     *
     * @param string $hash
     *
     * @return void
     */
    protected function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * Retrieves the contents of this file.
     *
     * @return string|null
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Sets the source contents for this file.
     *
     * @param string|null $source
     *
     * @return void
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * Returns the namespace aliases that have been defined in this file.
     *
     * @return Collection
     */
    public function getNamespaceAliases()
    {
        return $this->namespaceAliases;
    }

    /**
     * Sets the collection of namespace aliases for this file.
     *
     * @param Collection $namespaceAliases
     *
     * @return void
     */
    public function setNamespaceAliases(Collection $namespaceAliases)
    {
        $this->namespaceAliases = $namespaceAliases;
    }

    /**
     * Returns a list of all includes that have been declared in this file.
     *
     * @return Collection
     */
    public function getIncludes()
    {
        return $this->includes;
    }

    /**
     * Sets a list of all includes that have been declared in this file.
     *
     * @param Collection $includes
     *
     * @return void
     */
    public function setIncludes(Collection $includes)
    {
        $this->includes = $includes;
    }

    /**
     * Returns a list of constant descriptors contained in this file.
     *
     * @return Collection
     */
    public function getConstants()
    {
        return $this->constants;
    }

    /**
     * Sets a list of constant descriptors contained in this file.
     *
     * @param Collection $constants
     *
     * @return void
     */
    public function setConstants(Collection $constants)
    {
        $this->constants = $constants;
    }

    /**
     * Returns a list of function descriptors contained in this file.
     *
     * @return Collection|FunctionInterface[]
     */
    public function getFunctions()
    {
        return $this->functions;
    }

    /**
     * Sets a list of function descriptors contained in this file.
     *
     * @param Collection $functions
     *
     * @return void
     */
    public function setFunctions(Collection $functions)
    {
        $this->functions = $functions;
    }

    /**
     * Returns a list of class descriptors contained in this file.
     *
     * @return Collection|ClassInterface[]
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * Sets a list of class descriptors contained in this file.
     *
     * @param Collection $classes
     *
     * @return void
     */
    public function setClasses(Collection $classes)
    {
        $this->classes = $classes;
    }

    /**
     * Returns a list of interface descriptors contained in this file.
     *
     * @return Collection|InterfaceInterface[]
     */
    public function getInterfaces()
    {
        return $this->interfaces;
    }

    /**
     * Sets a list of interface descriptors contained in this file.
     *
     * @param Collection $interfaces
     *
     * @return void
     */
    public function setInterfaces(Collection $interfaces)
    {
        $this->interfaces = $interfaces;
    }

    /**
     * Returns a list of trait descriptors contained in this file.
     *
     * @return Collection|TraitInterface[]
     */
    public function getTraits()
    {
        return $this->traits;
    }

    /**
     * Sets a list of trait descriptors contained in this file.
     *
     * @param Collection $traits
     *
     * @return void
     */
    public function setTraits(Collection $traits)
    {
        $this->traits = $traits;
    }

    /**
     * Returns a series of markers contained in this file.
     *
     * A marker is a special inline comment that starts with a keyword and is followed by a single line description.
     *
     * Example:
     * ```
     * // TODO: This is an item that needs to be done.
     * ```
     *
     * @return Collection
     */
    public function getMarkers()
    {
        return $this->markers;
    }

    /**
     * Sets a series of markers contained in this file.
     *
     * @param Collection $markers
     *
     * @see getMarkers() for more information on markers.
     *
     * @return void
     */
    public function setMarkers(Collection $markers)
    {
        $this->markers = $markers;
    }

    /**
     * Returns a list of all errors in this file and all its child elements.
     *
     * @return Collection
     */
    public function getAllErrors()
    {
        $errors = $this->getErrors();

        $types = $this->getClasses()->merge($this->getInterfaces())->merge($this->getTraits());

        $elements = $this->getFunctions()->merge($this->getConstants())->merge($types);

        foreach ($elements as $element) {
            if (!$element) {
                continue;
            }

            $errors = $errors->merge($element->getErrors());
        }

        foreach ($types as $element) {
            if (!$element) {
                continue;
            }

            foreach ($element->getMethods() as $item) {
                if (!$item) {
                    continue;
                }
                $errors = $errors->merge($item->getErrors());
            }

            if (method_exists($element, 'getConstants')) {
                foreach ($element->getConstants() as $item) {
                    if (!$item) {
                        continue;
                    }
                    $errors = $errors->merge($item->getErrors());
                }
            }

            if (method_exists($element, 'getProperties')) {
                foreach ($element->getProperties() as $item) {
                    if (!$item) {
                        continue;
                    }
                    $errors = $errors->merge($item->getErrors());
                }
            }
        }

        return $errors;
    }

    /**
     * Sets the file path for this file relative to the project's root.
     *
     * @param string $path
     *
     * @return void
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Returns the file path relative to the project's root.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
}
