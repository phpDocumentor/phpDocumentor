<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor;

use InvalidArgumentException;
use phpDocumentor\Configuration\ApiSpecification;
use phpDocumentor\Descriptor\Builder\AssemblerFactory;
use phpDocumentor\Descriptor\Builder\AssemblerInterface;
use phpDocumentor\Descriptor\Filter\Filter;
use phpDocumentor\Descriptor\Filter\Filterable;
use phpDocumentor\Reflection\Php\Project;

final class ApiSetDescriptorBuilder
{
    private $name = '';

    /** @var AssemblerFactory */
    private $assemblerFactory;

    /** @var Filter */
    private $filter;

    /** @var ApiSpecification */
    private $apiSpecification;

    /** @var Project|null */
    private $project;

    public function __construct(
        AssemblerFactory $assemblerFactory,
        Filter $filterManager
    ) {
        $this->assemblerFactory = $assemblerFactory;
        $this->filter = $filterManager;
    }

    /**
     * Takes the given data and attempts to build a Descriptor from it.
     *
     * @param class-string<TDescriptor> $type
     *
     * @return TDescriptor|null
     *
     * @throws InvalidArgumentException If no Assembler could be found that matches the given data.
     *
     * @template TDescriptor of Descriptor
     */
    public function buildDescriptor(object $data, string $type) : ?Descriptor
    {
        $assembler = $this->getAssembler($data, $type);
        if (!$assembler) {
            throw new InvalidArgumentException(
                'Unable to build a Descriptor; the provided data did not match any Assembler ' .
                get_class($data)
            );
        }

        if ($assembler instanceof Builder\AssemblerAbstract) {
            $assembler->setBuilder($this);
        }

        // create Descriptor and populate with the provided data
        return $this->filterDescriptor($assembler->create($data));
    }

    /**
     * Attempts to find an assembler matching the given data.
     *
     * @param TInput $data
     * @param class-string<TDescriptor> $type
     *
     * @return AssemblerInterface<TDescriptor, TInput>|null
     *
     * @template TInput as object
     * @template TDescriptor as Descriptor
     */
    private function getAssembler(object $data, string $type) : ?AssemblerInterface
    {
        return $this->assemblerFactory->get($data, $type);
    }

    /**
     * Filters a descriptor, validates it, stores the validation results and returns the transmuted object or null
     * if it is supposed to be removed.
     *
     * @param TDescriptor $descriptor
     *
     * @return TDescriptor|null
     *
     * @template TDescriptor as Descriptor
     */
    private function filterDescriptor(Descriptor $descriptor) : ?Descriptor
    {
        if (!$descriptor instanceof Filterable) {
            return $descriptor;
        }

        // filter the descriptor; this may result in the descriptor being removed!
        $descriptor = $this->filter->filter($descriptor, $this->apiSpecification);

        return $descriptor;
    }

    public function getDefaultPackage() : string
    {
        return $this->apiSpecification['default-package-name'];
    }

    public function shouldIncludeSource(): bool
    {
        return $this->apiSpecification['include-source'];
    }

    public function setApiSpecification(ApiSpecification $apiSpecification) : void
    {
        $this->apiSpecification = $apiSpecification;
    }

    public function setProject(Project $project): void
    {
        $this->project = $project;
    }

    public function reset(): void
    {
        $this->project = null;
        $this->name = '';
    }

    public function createDescriptors(ApiSetDescriptor $documentationSet): void
    {
        foreach ($this->project->getFiles() as $file) {
            $descriptor = $this->buildDescriptor($file, FileDescriptor::class);
            if ($descriptor === null) {
                continue;
            }

            $documentationSet->addFile($descriptor);
        }

        foreach ($this->project->getNamespaces() as $namespace) {
            $descriptor = $this->buildDescriptor($namespace, NamespaceDescriptor::class);
            if ($descriptor === null) {
                continue;
            }

            $documentationSet->addNamespace($descriptor);
        }
    }
}
