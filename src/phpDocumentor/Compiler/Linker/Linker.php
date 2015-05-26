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

namespace phpDocumentor\Compiler\Linker;

use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\Interfaces\ProjectInterface;
use phpDocumentor\Descriptor\NamespaceDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\TraitDescriptor;
use phpDocumentor\Descriptor\Type\UnknownTypeDescriptor;

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
    const COMPILER_PRIORITY = 10000;

    const CONTEXT_MARKER = '@context';

    /** @var DescriptorAbstract[] */
    protected $elementList = array();

    /** @var string[][] */
    protected $substitutions = array();

    /** @var string[] Prevent cycles by tracking which objects have been analyzed */
    protected $processedObjects = array();

    /**
     * {@inheritDoc}
     */
    public function getDescription()
    {
        return 'Replace textual FQCNs with object aliases';
    }

    /**
     * Initializes the linker with a series of Descriptors to link to.
     *
     * @param array|string[][] $substitutions
     */
    public function __construct(array $substitutions)
    {
        $this->substitutions = $substitutions;
    }

    /**
     * Executes the linker.
     *
     * @param ProjectDescriptor $project Representation of the Object Graph that can be manipulated.
     *
     * @return void
     */
    public function execute(ProjectDescriptor $project)
    {
        $this->setObjectAliasesList($project->getIndexes()->elements->getAll());
        $this->substitute($project);
    }

    /**
     * Returns the list of substitutions for the linker.
     *
     * @return string[]
     */
    public function getSubstitutions()
    {
        return $this->substitutions;
    }

    /**
     * Sets the list of object aliases to resolve the FQSENs with.
     *
     * @param DescriptorAbstract[] $elementList
     *
     * @return void
     */
    public function setObjectAliasesList(array $elementList)
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
     * @param string|object|\Traversable|array $item
     * @param DescriptorAbstract|null          $container A descriptor that acts as container for all elements
     *     underneath or null if there is no current container.
     *
     * @return null|string|DescriptorAbstract
     */
    public function substitute($item, $container = null)
    {
        $result = null;

        if (is_string($item)) {
            $result = $this->findAlias($item, $container);
        } elseif (is_array($item) || ($item instanceof \Traversable && ! $item instanceof ProjectInterface)) {
            $isModified = false;
            foreach ($item as $key => $element) {
                $isModified = true;

                $element = $this->substitute($element, $container);
                if ($element !== null) {
                    $item[$key] = $element;
                }
            }
            if ($isModified) {
                $result = $item;
            }
        } elseif (is_object($item) && $item instanceof UnknownTypeDescriptor) {
            $alias  = $this->findAlias($item->getName());
            $result = $alias ?: $item;
        } elseif (is_object($item)) {
            $hash = spl_object_hash($item);
            if (isset($this->processedObjects[$hash])) {
                // if analyzed; just return
                return null;
            }

            $newContainer = ($this->isDescriptorContainer($item)) ? $item : $container;

            $this->processedObjects[$hash] = true;

            $objectClassName = get_class($item);
            $fieldNames = isset($this->substitutions[$objectClassName])
                ? $this->substitutions[$objectClassName]
                : array();

            foreach ($fieldNames as $fieldName) {
                $fieldValue = $this->findFieldValue($item, $fieldName);
                $response = $this->substitute($fieldValue, $newContainer);

                // if the returned response is not an object it must be grafted on the calling object
                if ($response !== null) {
                    $setter = 'set'.ucfirst($fieldName);
                    $item->$setter($response);
                }
            }
        }

        return $result;
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
     * @param string $fqsen
     * @param DescriptorAbstract|null $container
     *
     * @return DescriptorAbstract|string|null
     */
    public function findAlias($fqsen, $container = null)
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
            $namespaceMember  = $this->fetchElementByFqsen($namespaceContext);
            if ($namespaceMember) {
                return $namespaceMember;
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
     * @param object $object
     * @param string $fieldName
     *
     * @return string|object
     */
    public function findFieldValue($object, $fieldName)
    {
        $getter = 'get'.ucfirst($fieldName);

        return $object->$getter();
    }

    /**
     * Returns true if the given Descriptor is a container type.
     *
     * @param DescriptorAbstract|mixed $item
     *
     * @return bool
     */
    protected function isDescriptorContainer($item)
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
     * @param string $fqsen
     * @param DescriptorAbstract|null $container
     *
     * @return string
     */
    protected function replacePseudoTypes($fqsen, $container)
    {
        $pseudoTypes = array('self', '$this');
        foreach ($pseudoTypes as $pseudoType) {
            if ((strpos($fqsen, $pseudoType . '::') === 0 || $fqsen === $pseudoType) && $container) {
                $fqsen = $container->getFullyQualifiedStructuralElementName()
                    . substr($fqsen, strlen($pseudoType));
            }
        }

        return $fqsen;
    }

    /**
     * Returns true if the context marker is found in the given FQSEN.
     *
     * @param string $fqsen
     *
     * @return bool
     */
    protected function isContextMarkerInFqsen($fqsen)
    {
        return strpos($fqsen, self::CONTEXT_MARKER) !== false;
    }

    /**
     * Normalizes the given FQSEN as if the context marker represents a class/interface/trait as parent.
     *
     * @param string $fqsen
     * @param DescriptorAbstract $container
     *
     * @return string
     */
    protected function getTypeWithClassAsContext($fqsen, DescriptorAbstract $container)
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
     *
     * @param string             $fqsen
     * @param DescriptorAbstract $container
     *
     * @return string
     */
    protected function getTypeWithNamespaceAsContext($fqsen, DescriptorAbstract $container)
    {
        $namespace = $container instanceof NamespaceDescriptor ? $container : $container->getNamespace();
        $fqnn = $namespace instanceof NamespaceDescriptor
            ? $namespace->getFullyQualifiedStructuralElementName()
            : $namespace;

        return str_replace(self::CONTEXT_MARKER . '::', $fqnn . '\\', $fqsen);
    }

    /**
     * Attempts to find an element with the given Fqsen in the list of elements for this project and returns null if
     * it cannot find it.
     *
     * @param string $fqsen
     *
     * @return DescriptorAbstract|null
     */
    protected function fetchElementByFqsen($fqsen)
    {
        return isset($this->elementList[$fqsen]) ? $this->elementList[$fqsen] : null;
    }
}
