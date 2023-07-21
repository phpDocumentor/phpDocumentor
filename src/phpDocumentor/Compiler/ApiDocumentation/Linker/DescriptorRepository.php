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

namespace phpDocumentor\Compiler\ApiDocumentation\Linker;

use phpDocumentor\Descriptor\Interfaces\ClassInterface;
use phpDocumentor\Descriptor\Interfaces\ElementInterface;
use phpDocumentor\Descriptor\Interfaces\EnumInterface;
use phpDocumentor\Descriptor\Interfaces\InterfaceInterface;
use phpDocumentor\Descriptor\Interfaces\NamespaceInterface;
use phpDocumentor\Descriptor\Interfaces\TraitInterface;

use function sprintf;
use function str_contains;
use function str_replace;
use function str_starts_with;
use function strlen;
use function substr;

class DescriptorRepository
{
    private const CONTEXT_MARKER = '@context';

    /** @var array<array-key, ElementInterface> */
    private array $elementList = [];

    /**
     * Attempts to find a Descriptor object alias with the FQSEN of the element it represents.
     *
     * This method will try to fetch an element after normalizing the provided FQSEN. The FQSEN may contain references
     * (bindings) that can only be resolved during linking (such as `self`) or it may contain a context marker
     * {@see CONTEXT_MARKER}.
     *
     * If there is a context marker then this method will see if a child of the given container exists that matches the
     * element following the marker. If such a child does not exist in the current container then the namespace is
     * queried if a child exists there that matches.
     *
     * For example:
     *
     *     Given the Fqsen `@context::myFunction()` and the lastContainer `\My\Class` will this method first check
     *     to see if `\My\Class::myFunction()` exists; if it doesn't it will then check if `\My\myFunction()` exists.
     *
     * If neither element exists then this method assumes it is an undocumented class/trait/interface and change the
     * given FQSEN by returning the namespaced element name (thus in the example above that would be
     * `\My\myFunction()`). The calling method {@see substitute()} will then replace the value of the field containing
     * the context marker with this normalized string.
     *
     * @return ElementInterface|string|null
     */
    public function findAlias(string $fqsen, ElementInterface|null $container = null)
    {
        if ($container === null) {
            return $this->fetchElementByFqsen($fqsen);
        }

        $fqsen = $this->replacePseudoTypes($fqsen, $container);
        if (! $this->isContextMarkerInFqsen($fqsen)) {
            return $this->fetchElementByFqsen($fqsen);
        }

        // first exchange `@context::element` for `\My\Class::element` and if it exists, return that
        $classMember = $this->fetchElementByFqsen($this->getTypeWithClassAsContext($fqsen, $container));
        if ($classMember) {
            return $classMember;
        }

        // otherwise exchange `@context::element` for `\My\element` and if it exists, return that
        $namespaceContext = $this->getTypeWithNamespaceAsContext($fqsen, $container);
        $namespaceMember = $this->fetchElementByFqsen($namespaceContext);
        if ($namespaceMember) {
            return $namespaceMember;
        }

        // otherwise check if the element exists in the global namespace and if it exists, return that
        $globalNamespaceContext = $this->getTypeWithGlobalNamespaceAsContext($fqsen);
        $globalNamespaceMember = $this->fetchElementByFqsen($globalNamespaceContext);
        if ($globalNamespaceMember) {
            return $globalNamespaceMember;
        }

        // Otherwise we assume it is an undocumented class/interface/trait and return `\My\element` so
        // that the name containing the marker may be replaced by the class reference as string
        return $namespaceContext;
    }

    /**
     * Sets the list of object aliases to resolve the FQSENs with.
     *
     * @param array<ElementInterface> $elementList
     */
    public function setObjectAliasesList(array $elementList): void
    {
        $this->elementList = $elementList;
    }

    /**
     * Replaces pseudo-types, such as `self`, into a normalized version based on the last container that was
     * encountered.
     */
    private function replacePseudoTypes(string $fqsen, ElementInterface $container): string
    {
        $pseudoTypes = ['self', '$this'];
        foreach ($pseudoTypes as $pseudoType) {
            if (! str_starts_with($fqsen, $pseudoType . '::') && $fqsen !== $pseudoType) {
                continue;
            }

            return sprintf(
                '%s%s',
                (string) $container->getFullyQualifiedStructuralElementName(),
                substr($fqsen, strlen($pseudoType)),
            );
        }

        return $fqsen;
    }

    /**
     * Returns true if the context marker is found in the given FQSEN.
     */
    private function isContextMarkerInFqsen(string $fqsen): bool
    {
        return str_contains($fqsen, self::CONTEXT_MARKER);
    }

    /**
     * Normalizes the given FQSEN as if the context marker represents a class/interface/trait/enum as parent.
     */
    private function getTypeWithClassAsContext(string $fqsen, ElementInterface $container): string
    {
        if (
            ! $container instanceof ClassInterface
            && ! $container instanceof InterfaceInterface
            && ! $container instanceof TraitInterface
            && ! $container instanceof EnumInterface
        ) {
            return $fqsen;
        }

        $containerFqsen = $container->getFullyQualifiedStructuralElementName();

        return str_replace(self::CONTEXT_MARKER . '::', $containerFqsen . '::', $fqsen);
    }

    /**
     * Normalizes the given FQSEN as if the context marker represents a class/interface/trait as parent.
     */
    private function getTypeWithNamespaceAsContext(string $fqsen, ElementInterface $container): string
    {
        $namespace = $container instanceof NamespaceInterface ? $container : $container->getNamespace();
        $fqnn = $namespace instanceof NamespaceInterface
            ? $namespace->getFullyQualifiedStructuralElementName()
            : $namespace;

        return str_replace(self::CONTEXT_MARKER . '::', $fqnn . '\\', $fqsen);
    }

    /**
     * Normalizes the given FQSEN as if the context marker represents the global namespace as parent.
     */
    private function getTypeWithGlobalNamespaceAsContext(string $fqsen): string
    {
        return str_replace(self::CONTEXT_MARKER . '::', '\\', $fqsen);
    }

    /**
     * Attempts to find an element with the given Fqsen in the list of elements for this project and returns null if
     * it cannot find it.
     */
    private function fetchElementByFqsen(string $fqsen): ElementInterface|null
    {
        return $this->elementList[$fqsen] ?? null;
    }
}
