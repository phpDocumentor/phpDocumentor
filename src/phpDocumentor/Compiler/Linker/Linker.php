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
 *     contents of the ``ParentClass`` field should be substituted with another ClassDescriptor with the FQCN
 *     represented by the value of the ParentClass field. In addition (second element) it has an *Analyse* rule
 *     specifying that the contents of the ``Methods`` field should be interpreted by the linker. Because that field
 *     contains an array or Descriptor Collection will each element be analysed by the linker.
 *
 * As can be seen in the above example is it possible to analyse a tree structure and substitute FQSENs where
 * encountered.
 *
 * @see Rule\Substitute for more information on substitution
 * @see Rule\Analyse for more information on tree analysis.
 */
class Linker implements CompilerPassInterface
{
    const COMPILER_PRIORITY = 10000;

    /** @var DescriptorAbstract[] */
    protected $elementList = array();

    /** @var string[][] */
    protected $substitutions = array();

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
    public function findFqsen($object, $fieldName)
    {
        $getter = 'get'.ucfirst($fieldName);

        return $object->$getter();
    }

    /**
     * Substitutes the FQSEN in one or more fields of the given object with an object alias that is provided with the
     * object list in the setObjectAliasesList method.
     *
     * @param object $object
     *
     * @see self::setObjectAliasesList for the location where the object list is provided.
     *
     * @return void
     */
    public function substitute($object)
    {
        $objectClassName = get_class($object);
        $fieldNames = isset($this->substitutions[$objectClassName])
            ? $this->substitutions[$objectClassName]
            : array();

        foreach ($fieldNames as $fieldName) {
            $fqsen  = $this->findFqsen($object, $fieldName);
            if (is_object($fqsen)) {
                $this->substitute($fqsen);
            } elseif ($fqsen instanceof \Traversable || is_array($fqsen)) {
                foreach ($fqsen as $childObject) {
                    $this->substitute($childObject);
                }
            } elseif (is_string($fqsen)) {
                $result = $this->findAlias($fqsen);

                $setter = 'set'.ucfirst($fieldName);
                if (is_object($result)) {
                    $object->$setter($result);
                }
            }
        }
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
