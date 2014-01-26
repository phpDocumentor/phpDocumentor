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

namespace phpDocumentor\Descriptor;

use phpDocumentor\Descriptor\Builder\AssemblerFactory;
use phpDocumentor\Descriptor\Builder\Reflector\AssemblerAbstract;
use phpDocumentor\Descriptor\Filter\Filter;
use phpDocumentor\Descriptor\Filter\Filterable;
use phpDocumentor\Descriptor\Validator\Error;
use Psr\Log\LogLevel;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator;

/**
 * Builds a Project Descriptor and underlying tree.
 */
class ProjectDescriptorBuilder
{
    /** @var string */
    const DEFAULT_PROJECT_NAME = 'Untitled project';

    /** @var AssemblerFactory $assemblerFactory */
    protected $assemblerFactory;

    /** @var Validator $validator */
    protected $validator;

    /** @var Filter $filter */
    protected $filter;

    /** @var ProjectDescriptor $project */
    protected $project;

    public function __construct(AssemblerFactory $assemblerFactory, Filter $filterManager, Validator $validator)
    {
        $this->assemblerFactory = $assemblerFactory;
        $this->validator        = $validator;
        $this->filter           = $filterManager;
    }

    public function createProjectDescriptor()
    {
        $this->project = new ProjectDescriptor(self::DEFAULT_PROJECT_NAME);
    }

    public function setProjectDescriptor(ProjectDescriptor $projectDescriptor)
    {
        $this->project = $projectDescriptor;
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

    public function buildFileUsingSourceData($data)
    {
        $descriptor = $this->buildDescriptor($data);
        if (!$descriptor) {
            return;
        }

        $this->getProjectDescriptor()->getFiles()->set($descriptor->getPath(), $descriptor);
    }

    /**
     * Takes the given data and attempts to build a Descriptor from it.
     *
     * @param mixed $data
     *
     * @throws \InvalidArgumentException if no Assembler could be found that matches the given data.
     *
     * @return DescriptorAbstract|null
     */
    public function buildDescriptor($data)
    {
        $assembler = $this->getAssembler($data);
        if (!$assembler) {
            throw new \InvalidArgumentException(
                'Unable to build a Descriptor; the provided data did not match any Assembler '. get_class($data)
            );
        }

        if ($assembler instanceof Builder\AssemblerAbstract) {
            $assembler->setBuilder($this);
        }

        // create Descriptor and populate with the provided data
        $descriptor = $assembler->create($data);
        if (!$descriptor) {
            return null;
        }

        // filter the descriptor; this may result in the descriptor being removed!
        $descriptor = $this->filter($descriptor);
        if (!$descriptor) {
            return null;
        }

        // Validate the descriptor and store any errors
        $descriptor->setErrors($this->validate($descriptor));

        return $descriptor;
    }

    /**
     * Attempts to find an assembler matching the given data.
     *
     * @param mixed $data
     *
     * @return AssemblerAbstract
     */
    public function getAssembler($data)
    {
        return $this->assemblerFactory->get($data);
    }

    /**
     * Analyzes a Descriptor and alters its state based on its state or even removes the descriptor.
     *
     * @param Filterable $descriptor
     *
     * @return Filterable
     */
    public function filter(Filterable $descriptor)
    {
        return $this->filter->filter($descriptor);
    }

    /**
     * Validates the contents of the Descriptor and outputs warnings and error if something is amiss.
     *
     * @param DescriptorAbstract $descriptor
     *
     * @return Collection
     */
    public function validate($descriptor)
    {
        $violations = $this->validator->validate($descriptor);
        $errors = new Collection();

        /** @var ConstraintViolation $violation */
        foreach ($violations as $violation) {
            $errors->add(
                new Error(
                    LogLevel::ERROR, // TODO: Make configurable
                    $violation->getMessageTemplate(),
                    $descriptor->getLine(),
                    $violation->getMessageParameters() + array($descriptor->getFullyQualifiedStructuralElementName())
                )
            );
        }

        return $errors;
    }
}
