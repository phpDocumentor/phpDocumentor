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

class FileDescriptor extends DescriptorAbstract implements Interfaces\FileInterface
{
    /** @var string */
    protected $hash;

    /** @var string */
    protected $path = '';

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

    public function getHash()
    {
        return $this->hash;
    }

    protected function setHash($hash)
    {
        $this->hash = $hash;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function setSource($source)
    {
        $this->source = $source;
    }

    public function getNamespaceAliases()
    {
        return $this->namespace_aliases;
    }

    public function setNamespaceAliases(Collection $namespace_aliases)
    {
        $this->namespace_aliases = $namespace_aliases;
    }

    /**
     * @return Collection
     */
    public function getIncludes()
    {
        return $this->includes;
    }

    public function setIncludes(Collection $includes)
    {
        $this->includes = $includes;
    }

    public function getConstants()
    {
        return $this->constants;
    }

    public function setConstants(Collection $constants)
    {
        $this->constants = $constants;
    }

    public function getFunctions()
    {
        return $this->functions;
    }

    public function setFunctions(Collection $functions)
    {
        $this->functions = $functions;
    }

    /**
     * @return Collection|ClassDescriptor[]
     */
    public function getClasses()
    {
        return $this->classes;
    }

    public function setClasses(Collection $classes)
    {
        $this->classes = $classes;
    }

    public function getInterfaces()
    {
        return $this->interfaces;
    }

    public function setInterfaces(Collection $interfaces)
    {
        $this->interfaces = $interfaces;
    }

    public function getTraits()
    {
        return $this->traits;
    }

    public function setTraits(Collection $traits)
    {
        $this->traits = $traits;
    }

    /**
     * @return Collection
     */
    public function getMarkers()
    {
        return $this->markers;
    }

    /**
     * @param Collection $markers
     */
    public function setMarkers(Collection $markers)
    {
        $this->markers = $markers;
    }

    public function getAllErrors()
    {
        $errors = $this->getErrors();

        foreach ($this->getFunctions() as $element) {
            if (!$element) {
                continue;
            }
            $errors = $errors->merge($element->getErrors());
        }

        foreach ($this->getConstants() as $element) {
            if (!$element) {
                continue;
            }
            $errors = $errors->merge($element->getErrors());
        }

        foreach ($this->getClasses() as $element) {
            $errors = $errors->merge($element->getErrors());
            foreach ($element->getConstants() as $item) {
                if (!$item) {
                    continue;
                }
                $errors = $errors->merge($item->getErrors());
            }
            foreach ($element->getProperties() as $item) {
                if (!$item) {
                    continue;
                }
                $errors = $errors->merge($item->getErrors());
            }
            foreach ($element->getMethods() as $item) {
                if (!$item) {
                    continue;
                }
                $errors = $errors->merge($item->getErrors());
            }
        }

        foreach ($this->getInterfaces() as $element) {
            $errors = $errors->merge($element->getErrors());
            foreach ($element->getConstants() as $item) {
                if (!$item) {
                    continue;
                }
                $errors = $errors->merge($item->getErrors());
            }
            foreach ($element->getMethods() as $item) {
                if (!$item) {
                    continue;
                }
                $errors = $errors->merge($item->getErrors());
            }
        }

        foreach ($this->getTraits() as $element) {
            $errors = $errors->merge($element->getErrors());
            foreach ($element->getProperties() as $item) {
                if (!$item) {
                    continue;
                }
                $errors = $errors->merge($item->getErrors());
            }
            foreach ($element->getMethods() as $item) {
                if (!$item) {
                    continue;
                }
                $errors = $errors->merge($item->getErrors());
            }
        }

        return $errors;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
}
