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

namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Compiler\CompilableSubject;
use phpDocumentor\Configuration\ApiSpecification;
use phpDocumentor\Reflection\Fqsen;

interface ApiDocumentationSet extends DocumentationSetInterface, CompilableSubject
{
    public function getSettings(): ApiSpecification;

    /**
     * Finds a structural element with the given FQSEN in this Documentation Set, or returns null when it
     * could not be found.
     */
    public function findElement(Fqsen $fqsen): ElementInterface|null;

    /**
     * Returns the namespace for this element (defaults to global "\")
     *
     * @return NamespaceInterface|string
     */
    public function getNamespace();

    public function getPackage(): PackageInterface|null;
}
