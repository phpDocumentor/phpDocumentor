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

namespace phpDocumentor\Compiler\Linker;

use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\Descriptor;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\NamespaceDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\TraitDescriptor;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Type;
use Webmozart\Assert\Assert;

use function get_class;
use function is_array;
use function is_iterable;
use function is_object;
use function is_string;
use function spl_object_hash;
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

    /** @var array<class-string, array<string>> */
    private $substitutions;

    /** @var string[] Prevent cycles by tracking which objects have been analyzed */
    private $processedObjects = [];

    /** @var DescriptorRepository */
    private $descriptorRepository;

    public function getDescription(): string
    {
        return 'Replace textual FQCNs with object aliases';
    }

    /**
     * Initializes the linker with a series of Descriptors to link to.
     *
     * @param array<class-string, array<string>> $substitutions
     */
    public function __construct(array $substitutions, DescriptorRepository $descriptorRepository)
    {
        $this->substitutions = $substitutions;
        $this->descriptorRepository = $descriptorRepository;
    }

    public function execute(ProjectDescriptor $project): void
    {
        $this->descriptorRepository->setObjectAliasesList($project->getIndexes()->elements->getAll());
        $this->substitute($project);
    }

    /**
     * Returns the list of substitutions for the linker.
     *
     * @return string[][]
     */
    public function getSubstitutions(): array
    {
        return $this->substitutions;
    }

    /**
     * Substitutes the given item or its children's FQCN with an object alias.
     *
     * This method may do either of the following depending on the item's type
     *
     * FQSEN or String
     *     If the given item is a string then this method will attempt to find an appropriate Class, Interface or
     *     TraitDescriptor object and return that. See {@see DescriptorRepository::findAlias()} for more information
     *     on the normalization of these strings.
     *
     * Array or Traversable
     *     Iterate through each item, pass each key's contents to a new call to substitute and replace the key's
     *     contents if the contents is not an object (objects automatically update and this saves performance).
     *
     * Object
     *     Determines all eligible substitutions using the substitutions property, construct a getter and retrieve
     *     the field's contents. Pass these contents to a new call of substitute and use a setter to replace the field's
     *     contents if anything other than null is returned.
     *
     * The Container is a descriptor that acts as container for all elements underneath or null if there is no current
     * container.
     *
     * This method will return null if no substitution was possible and all of the above should not update the parent
     * item when null is passed.
     *
     * @param string|Fqsen|Type|Collection<mixed>|array<mixed>|Descriptor $item
     *
     * @return string|DescriptorAbstract|Collection<string|DescriptorAbstract>|array<string|DescriptorAbstract>|null
     */
    public function substitute($item, ?DescriptorAbstract $container = null)
    {
        if ($item instanceof Type) {
            return null;
        }

        if ($item instanceof Fqsen) {
            return $this->descriptorRepository->findAlias((string) $item, $container);
        }

        if (is_string($item)) {
            return $this->descriptorRepository->findAlias($item, $container);
        }

        if (is_iterable($item)) {
            Assert::true(is_array($item) || $item instanceof Collection);

            return $this->substituteChildrenOfCollection($item, $container);
        }

        if (!is_object($item)) {
            return null;
        }

        $this->substituteMembersOfObject($item, $container);

        return null;
    }

    /**
     * @param array<string|DescriptorAbstract>|Collection<string|DescriptorAbstract> $collection
     *
     * @return array<string|DescriptorAbstract>|Collection<string|DescriptorAbstract>|null
     */
    private function substituteChildrenOfCollection(iterable $collection, ?DescriptorAbstract $container): ?iterable
    {
        $isModified = false;
        foreach ($collection as $key => $element) {
            $element = $this->substitute($element, $container);
            if ($element === null) {
                continue;
            }

            $isModified = true;
            $collection[$key] = $element;
        }

        if ($isModified) {
            return $collection;
        }

        return null;
    }

    /**
     * Returns the value of a field in the given object.
     *
     * @return string|object
     */
    private function findFieldValue(object $object, string $fieldName)
    {
        $getter = 'get' . ucfirst($fieldName);

        return $object->{$getter}();
    }

    /**
     * Returns true if the given Descriptor is a container type.
     *
     * @psalm-assert DescriptorAbstract $item
     */
    private function isDescriptorContainer(object $item): bool
    {
        return $item instanceof FileDescriptor
            || $item instanceof NamespaceDescriptor
            || $item instanceof ClassDescriptor
            || $item instanceof TraitDescriptor
            || $item instanceof InterfaceDescriptor;
    }

    private function substituteMembersOfObject(object $object, ?DescriptorAbstract $container): void
    {
        $hash = spl_object_hash($object);
        if (isset($this->processedObjects[$hash])) {
            // if analyzed; just return null to indicate processing is already done
            return;
        }

        $newContainer = $this->isDescriptorContainer($object) ? $object : $container;

        $this->processedObjects[$hash] = $hash;

        $objectClassName = get_class($object);
        $fieldNames = $this->substitutions[$objectClassName] ?? [];

        foreach ($fieldNames as $fieldName) {
            $fieldValue = $this->findFieldValue($object, $fieldName);
            $response = $this->substitute($fieldValue, $newContainer);

            if ($response === null) {
                continue;
            }

            // TODO Can we find another solution for this?
            $setter = 'set' . ucfirst($fieldName);
            $object->{$setter}($response);
        }
    }
}
