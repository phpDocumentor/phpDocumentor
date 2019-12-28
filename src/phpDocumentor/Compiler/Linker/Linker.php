<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Compiler\Linker;

use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\Interfaces\ProjectInterface;
use phpDocumentor\Descriptor\NamespaceDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\TraitDescriptor;
use phpDocumentor\Descriptor\Type\UnknownTypeDescriptor;
use phpDocumentor\Reflection\Fqsen;
use Traversable;
use function get_class;
use function is_array;
use function is_object;
use function is_string;
use function spl_object_hash;
use function str_replace;
use function strlen;
use function strpos;
use function substr;
use function ucfirst;

/**
 * The linker contains all rules to replace FQSENs in the ProjectDescriptor with aliases to objects.
 *
 * This object contains a list of class FQCNs for Descriptors and their associated linker rules.
 *
 * An example scenario should be:
 *
 *     The Descriptor ``\phpDocumentor\Descriptor\ClassDescriptor`` has a *Substitute* rule determining that the
 *     contents of the ``Parent`` field should be substituted with another ClassDescriptor with the FQCN
 *     represented by the value of the Parent field. In addition (second element) it has an *Analyse* rule
 *     specifying that the contents of the ``Methods`` field should be interpreted by the linker. Because that field
 *     contains an array or Descriptor Collection will each element be analysed by the linker.
 *
 * As can be seen in the above example is it possible to analyse a tree structure and substitute FQSENs where
 * encountered.
 */
class Linker implements CompilerPassInterface
{
    public const COMPILER_PRIORITY = 10000;

    public const CONTEXT_MARKER = '@context';

    /** @var DescriptorAbstract[] */
    private $elementList = [];

    /** @var string[][] */
    private $substitutions = [];

    /** @var string[] Prevent cycles by tracking which objects have been analyzed */
    private $processedObjects = [];

    public function getDescription() : string
    {
        return 'Replace textual FQCNs with object aliases';
    }

    /**
     * Initializes the linker with a series of Descriptors to link to.
     *
     * @param string[][] $substitutions
     */
    public function __construct(array $substitutions)
    {
        $this->substitutions = $substitutions;
    }

    public function execute(ProjectDescriptor $project) : void
    {
        $this->setObjectAliasesList($project->getIndexes()->elements->getAll());
        $this->substitute($project);
    }

    /**
     * Returns the list of substitutions for the linker.
     *
     * @return string[][]
     */
    public function getSubstitutions() : array
    {
        return $this->substitutions;
    }

    /**
     * Sets the list of object aliases to resolve the FQSENs with.
     *
     * @param DescriptorAbstract[] $elementList
     */
    public function setObjectAliasesList(array $elementList) : void
    {
        $this->elementList = $elementList;
    }

    /**
     * Substitutes the given item or its children's FQCN with an object alias.
     *
     * This method may do either of the following depending on the item's type
     *
     * String
     *     If the given item is a string then this method will attempt to find an appropriate Class, Interface or
     *     TraitDescriptor object and return that. See {@see findAlias()} for more information on the normalization
     *     of these strings.
     *
     * Array or Traversable
     *     Iterate through each item, pass each key's contents to a new call to substitute and replace the key's
     *     contents if the contents is not an object (objects automatically update and saves performance).
     *
     * Object
     *     Determines all eligible substitutions using the substitutions property, construct a getter and retrieve
     *     the field's contents. Pass these contents to a new call of substitute and use a setter to replace the field's
     *     contents if anything other than null is returned.
     *
     * This method will return null if no substitution was possible and all of the above should not update the parent
     * item when null is passed.
     *
     * @param string|object|Traversable|array $item
     * @param DescriptorAbstract|null $container A descriptor that acts as container for all elements
     *                                        underneath or null if there is no current container.
     *
     * @return string|DescriptorAbstract|null
     */
    public function substitute($item, $container = null)
    {
        if ($item instanceof Fqsen) {
            return $this->findAlias((string) $item, $container);
        }

        if (is_string($item)) {
            return $this->findAlias($item, $container);
        }

        if (is_array($item) || ($item instanceof Traversable && !$item instanceof ProjectInterface)) {
            $isModified = false;
            foreach ($item as $key => $element) {
                $isModified = true;

                $element = $this->substitute($element, $container);
                if ($element === null) {
                    continue;
                }

                $item[$key] = $element;
            }

            if ($isModified) {
                $result = $item;
            }

            return $result;
        }

        if ($item instanceof UnknownTypeDescriptor) {
            return $this->findAlias($item->getName(), $container) ?: $item;
        }

        if (is_object($item)) {
            $hash = spl_object_hash($item);
            if (isset($this->processedObjects[$hash])) {
                // if analyzed; just return
                return null;
            }

            $newContainer = $this->isDescriptorContainer($item) ? $item : $container;

            $this->processedObjects[$hash] = $hash;

            $objectClassName = get_class($item);
            $fieldNames = $this->substitutions[$objectClassName] ?? [];

            foreach ($fieldNames as $fieldName) {
                $fieldValue = $this->findFieldValue($item, $fieldName);
                $response = $this->substitute($fieldValue, $newContainer);

                // if the returned response is not an object it must be grafted on the calling object
                if ($response === null) {
                    continue;
                }

                // TODO Can we find another solution for this?
                $setter = 'set' . ucfirst($fieldName);
                $item->{$setter}($response);
            }
        }

        return null;
    }

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
     * @return DescriptorAbstract|string|null
     */
    public function findAlias(string $fqsen, ?DescriptorAbstract $container = null)
    {
        $fqsen = $this->replacePseudoTypes($fqsen, $container);

        if ($this->isContextMarkerInFqsen($fqsen) && $container instanceof DescriptorAbstract) {
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

        return $this->fetchElementByFqsen($fqsen);
    }

    /**
     * Returns the value of a field in the given object.
     *
     * @return string|object
     */
    public function findFieldValue(object $object, string $fieldName)
    {
        $getter = 'get' . ucfirst($fieldName);

        return $object->{$getter}();
    }

    /**
     * Returns true if the given Descriptor is a container type.
     */
    private function isDescriptorContainer(object $item) : bool
    {
        return $item instanceof FileDescriptor
            || $item instanceof NamespaceDescriptor
            || $item instanceof ClassDescriptor
            || $item instanceof TraitDescriptor
            || $item instanceof InterfaceDescriptor;
    }

    /**
     * Replaces pseudo-types, such as `self`, into a normalized version based on the last container that was
     * encountered.
     *
     * @todo can we remove the nullable from this somehow to make the method contents simpler
     */
    private function replacePseudoTypes(string $fqsen, ?DescriptorAbstract $container) : string
    {
        $pseudoTypes = ['self', '$this'];
        foreach ($pseudoTypes as $pseudoType) {
            if ((strpos($fqsen, $pseudoType . '::') !== 0 && $fqsen !== $pseudoType) || !$container) {
                continue;
            }

            $fqsen = $container->getFullyQualifiedStructuralElementName()
                . substr($fqsen, strlen($pseudoType));
        }

        return $fqsen;
    }

    /**
     * Returns true if the context marker is found in the given FQSEN.
     */
    private function isContextMarkerInFqsen(string $fqsen) : bool
    {
        return strpos($fqsen, self::CONTEXT_MARKER) !== false;
    }

    /**
     * Normalizes the given FQSEN as if the context marker represents a class/interface/trait as parent.
     */
    private function getTypeWithClassAsContext(string $fqsen, DescriptorAbstract $container) : string
    {
        if (!$container instanceof ClassDescriptor
            && !$container instanceof InterfaceDescriptor
            && !$container instanceof TraitDescriptor
        ) {
            return $fqsen;
        }

        $containerFqsen = $container->getFullyQualifiedStructuralElementName();

        return str_replace(self::CONTEXT_MARKER . '::', $containerFqsen . '::', $fqsen);
    }

    /**
     * Normalizes the given FQSEN as if the context marker represents a class/interface/trait as parent.
     */
    private function getTypeWithNamespaceAsContext(string $fqsen, DescriptorAbstract $container) : string
    {
        $namespace = $container instanceof NamespaceDescriptor ? $container : $container->getNamespace();
        $fqnn = $namespace instanceof NamespaceDescriptor
            ? $namespace->getFullyQualifiedStructuralElementName()
            : $namespace;

        return str_replace(self::CONTEXT_MARKER . '::', $fqnn . '\\', $fqsen);
    }

    /**
     * Normalizes the given FQSEN as if the context marker represents the global namespace as parent.
     */
    private function getTypeWithGlobalNamespaceAsContext(string $fqsen) : string
    {
        return str_replace(self::CONTEXT_MARKER . '::', '\\', $fqsen);
    }

    /**
     * Attempts to find an element with the given Fqsen in the list of elements for this project and returns null if
     * it cannot find it.
     */
    private function fetchElementByFqsen(string $fqsen) : ?DescriptorAbstract
    {
        return $this->elementList[$fqsen] ?? null;
    }
}
