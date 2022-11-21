<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Traits;

use phpDocumentor\Descriptor\NamespaceDescriptor;

trait HasNamespace
{
    /** @var NamespaceDescriptor|string $namespace The namespace for this element */
    protected $namespace = '';

    /**
     * Sets the namespace (name) for this element.
     *
     * @internal should not be called by any other class than the assemblers
     *
     * @param NamespaceDescriptor|string $namespace
     */
    public function setNamespace($namespace): void
    {
        $this->namespace = $namespace;
    }

    /**
     * Returns the namespace for this element (defaults to global "\")
     *
     * @return NamespaceDescriptor|string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }
}
