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

namespace phpDocumentor\Descriptor;

use InvalidArgumentException;
use OutOfRangeException;
use phpDocumentor\Configuration\ApiSpecification;
use phpDocumentor\Descriptor\Builder\AssemblerFactory;
use phpDocumentor\Descriptor\Builder\AssemblerInterface;
use phpDocumentor\Descriptor\Filter\Filter;
use phpDocumentor\Descriptor\Filter\Filterable;
use phpDocumentor\Descriptor\ProjectDescriptor\WithCustomSettings;
use phpDocumentor\Reflection\Php\Project;

/**
 * Builds a Project Descriptor and underlying tree.
 */
class ProjectDescriptorBuilder
{
    final public const DEFAULT_PROJECT_NAME = 'Untitled project';

    /** @var AssemblerFactory $assemblerFactory */
    protected $assemblerFactory;

    /** @var Filter $filter */
    protected $filter;

    /** @var ProjectDescriptor $project */
    protected $project;

    private ApiSpecification $apiSpecification;

    private string $defaultPackageName;

    /** @param iterable<WithCustomSettings> $servicesWithCustomSettings */
    public function __construct(
        AssemblerFactory $assemblerFactory,
        Filter $filterManager,
        private readonly iterable $servicesWithCustomSettings = [],
    ) {
        $this->assemblerFactory = $assemblerFactory;
        $this->filter = $filterManager;
    }

    public function createProjectDescriptor(): void
    {
        $this->project = new ProjectDescriptor(self::DEFAULT_PROJECT_NAME);

        // Ensure all custom settings from other services are loaded, even if no other custom settings will
        // be set later on.
        $this->setCustomSettings([]);
    }

    /**
     * Returns the project descriptor that is being built.
     */
    public function getProjectDescriptor(): ProjectDescriptor
    {
        return $this->project;
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
    public function buildDescriptor(object $data, string $type): Descriptor|null
    {
        $assembler = $this->getAssembler($data, $type);
        if (! $assembler) {
            throw new InvalidArgumentException(
                'Unable to build a Descriptor; the provided data did not match any Assembler ' .
                $data::class,
            );
        }

        if ($assembler instanceof Builder\AssemblerAbstract) {
            $assembler->setBuilder($this);
        }

        // create Descriptor and populate with the provided data
        $descriptor = $assembler->create($data);
        if ($descriptor === null) {
            return null;
        }

        return $this->filterDescriptor($descriptor);
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
    public function getAssembler(object $data, string $type): AssemblerInterface|null
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
    protected function filterDescriptor(Descriptor $descriptor): Descriptor|null
    {
        if (! $descriptor instanceof Filterable) {
            return $descriptor;
        }

        // filter the descriptor; this may result in the descriptor being removed!
        $descriptor = $this->filter->filter($descriptor, $this->apiSpecification);

        return $descriptor;
    }

    public function usingApiSpecification(ApiSpecification $apiSpecification): void
    {
        $this->apiSpecification = $apiSpecification;
    }

    public function populateApiDocumentationSet(ApiSetDescriptor $apiSet, Project $project): void
    {
        foreach ($project->getFiles() as $file) {
            $descriptor = $this->buildDescriptor($file, FileDescriptor::class);
            if ($descriptor === null) {
                continue;
            }

            $apiSet->getFiles()->set($descriptor->getPath(), $descriptor);
        }

        $namespaces = $apiSet->getIndexes()->fetch('namespaces', new Collection());

        foreach ($project->getNamespaces() as $namespace) {
            $namespaces->set(
                (string) $namespace->getFqsen(),
                $this->buildDescriptor($namespace, NamespaceDescriptor::class),
            );
        }
    }

    public function usingDefaultPackageName(string $name): void
    {
        $this->defaultPackageName = $name;
    }

    public function getDefaultPackageName(): string
    {
        return $this->defaultPackageName;
    }

    public function setVisibility(int $visibility): void
    {
        $this->project->getSettings()->setVisibility($visibility);
    }

    public function setName(string $title): void
    {
        $this->project->setName($title);
    }

    /** @param Collection<string> $partials */
    public function setPartials(Collection $partials): void
    {
        $this->project->setPartials($partials);
    }

    /** @param array<string, string> $customSettings */
    public function setCustomSettings(array $customSettings): void
    {
        foreach ($this->servicesWithCustomSettings as $service) {
            // We assume that the custom settings have the non-default settings and we should not override those;
            // that is why we merge the custom settings on top of the default settings; this will cause the overrides
            // to remain in place.
            $customSettings = [...$service->getDefaultSettings(), ...$customSettings];
        }

        $this->project->getSettings()->setCustom($customSettings);
    }

    public function addVersion(VersionDescriptor $version): void
    {
        if ($this->project->getVersions()->count() >= 1) {
            throw new OutOfRangeException(
                'phpDocumentor only supports 1 version at the moment, support for multiple versions is being worked on',
            );
        }

        $this->project->getVersions()->add($version);
    }
}
