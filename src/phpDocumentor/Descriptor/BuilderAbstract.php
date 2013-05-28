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

/**
 * Base class for constructing a ProjectDescriptor with Descriptor tree.
 *
 * This class takes a Project Descriptor and allows the user to add all element types onto it. These
 * element types may be derived from an undescribed data source so that it is possible to have various
 * inputs.
 *
 * Example usages are (these do not have to exist):
 *
 * - Builder\Reflection, where the elements that are interpreted by phpDocumentor's Static Reflection library are
 *   converted into matching Descriptors.
 * - Builder\Database, where elements are stored in a database and an id is provided as data
 *
 * And more of these are imaginable.
 */
abstract class BuilderAbstract
{
    /** @var string  */
    const DEFAULT_PROJECT_NAME = 'Untitled project';

    /** @var ProjectDescriptor $project */
    protected $project;

    /**
     * Initializes this builder with a new or existing Project Descriptor.
     *
     * @param ProjectDescriptor $project
     */
    public function __construct(ProjectDescriptor $project = null)
    {
        $this->project = $project ?: new ProjectDescriptor(self::DEFAULT_PROJECT_NAME);
    }

    /**
     * Returns the project descriptor that is being built.
     *
     * @return ProjectDescriptor
     */
    public function getProjectDescriptor()
    {
        return $this->project;
    }

    /**
     * Verifies whether the given visibility is allowed to be included in the Descriptors.
     *
     * This method is used anytime a Descriptor is added to a collection (for example, when adding a Method to a Class)
     * to determine whether the visibility of that element is matches what the user has specified when it ran
     * phpDocumentor.
     *
     * @param integer $visibility One of the visibility constants of the ProjectDescriptor class.
     *
     * @see ProjectDescriptor where the visibility is stored and that declares the constants to use.
     *
     * @return boolean
     */
    public function isVisibilityAllowed($visibility)
    {
        return $this->getProjectDescriptor()->isVisibilityAllowed($visibility);
    }

    abstract public function setFile($data);
}
