<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Traits;

use phpDocumentor\Descriptor\Interfaces\NamespaceInterface;

trait HasNamespace
{
    /** @var NamespaceInterface|string $namespace The namespace for this element */
    protected $namespace = '';

    /**
     * Sets the namespace (name) for this element.
     *
     * @internal should not be called by any other class than the assemblers
     *
     * @param NamespaceInterface|string $namespace
     */
    public function setNamespace($namespace): void
    {
        $this->namespace = $namespace;
    }

    /**
     * Returns the namespace for this element (defaults to global "\")
     *
     * @return NamespaceInterface|string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }
}
