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

namespace phpDocumentor\Compiler\Linker;

use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\ProjectDescriptor;

/**
 * The linker contains all rules to replace FQSENs in the ProjectDescriptor with aliases to objects.
 *
 * This object contains a list of class FQCNs for Descriptors and their associated linker rules.
 *
 * An example scenario sould be:
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
     * @param string[] $substitutions
     */
    public function __construct(array $substitutions)
    {
        $this->substitutions = $substitutions;
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
     * Attempts to find a Descriptor object alias with the FQSEN of the element it represents.
     *
     * @param string $fqsen
     *
     * @return DescriptorAbstract|null
     */
    public function findAlias($fqsen)
    {
        return isset($this->elementList[$fqsen]) ? $this->elementList[$fqsen] : null;
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
     * Substitutes the given item or its children's FQCN with an object alias.
     *
     * This method may do either of the following depending on the item's type
     *
     * String
     *     If the given item is a string then this method will attempt to find an appropriate Class, Interface or
     *     TraitDescriptor object and return that.
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
     *
     * @return null|string|DescriptorAbstract
     */
    public function substitute($item)
    {
        $result = null;

        if (is_string($item)) {
            $result = $this->findAlias($item);
        } elseif (is_array($item) || $item instanceof \Traversable) {
            $isModified = false;
            foreach ($item as $key => $element) {
                $isModified = true;

                $element = $this->substitute($element);
                if ($element !== null) {
                    $item[$key] = $element;
                }
            }
            if ($isModified) {
                $result = $item;
            }
        } elseif (is_object($item)) {
            $hash = spl_object_hash($item);
            if (isset($this->processedObjects[$hash])) {
                // if analyzed; just return
                return null;
            }
            $this->processedObjects[$hash] = true;

            $objectClassName = get_class($item);
            $fieldNames = isset($this->substitutions[$objectClassName])
                ? $this->substitutions[$objectClassName]
                : array();

            foreach ($fieldNames as $fieldName) {
                $fieldValue = $this->findFieldValue($item, $fieldName);
                $response = $this->substitute($fieldValue);

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
}
