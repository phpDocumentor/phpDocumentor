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
use phpDocumentor\Configuration\ApiSpecification;
use phpDocumentor\Descriptor\Builder\AssemblerFactory;
use phpDocumentor\Descriptor\Builder\AssemblerInterface;
use phpDocumentor\Descriptor\Filter\Filter;
use phpDocumentor\Descriptor\Filter\Filterable;
use phpDocumentor\Descriptor\ProjectDescriptor\WithCustomSettings;
use phpDocumentor\Reflection\Php\Project;

use function array_merge;
use function get_class;

/**
 * Builds a Project Descriptor and underlying tree.
 */
class ProjectDescriptorBuilder
{
    /** @var string */
    public const DEFAULT_PROJECT_NAME = 'Untitled project';

    /** @var AssemblerFactory $assemblerFactory */
    protected $assemblerFactory;

    /** @var Filter $filter */
    protected $filter;

    /** @var ProjectDescriptor $project */
    protected $project;

    /** @var iterable<WithCustomSettings> */
    private $servicesWithCustomSettings;

    /** @var ApiSpecification */
    private $apiSpecification;

    /**
     * @param iterable<WithCustomSettings> $servicesWithCustomSettings
     */
    public function __construct(
        AssemblerFactory $assemblerFactory,
        Filter $filterManager,
        iterable $servicesWithCustomSettings = []
    ) {
        $this->assemblerFactory = $assemblerFactory;
        $this->filter = $filterManager;
        $this->servicesWithCustomSettings = $servicesWithCustomSettings;
    }

    public function createProjectDescriptor(): void
    {
        $this->project = new ProjectDescriptor(self::DEFAULT_PROJECT_NAME);
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
    public function buildDescriptor(object $data, string $type): ?Descriptor
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
    public function getAssembler(object $data, string $type): ?AssemblerInterface
    {
        return $this->assemblerFactory->get($data, $type);
    }

    /**
     * Analyzes a Descriptor and alters its state based on its state or even removes the descriptor.
     *
     * @param TDescriptor $descriptor
     *
     * @return TDescriptor|null
     *
     * @template TDescriptor as Filterable
     */
    public function filter(Filterable $descriptor): ?Filterable
    {
        return $this->filter->filter($descriptor, $this->apiSpecification);
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
    protected function filterDescriptor(Descriptor $descriptor): ?Descriptor
    {
        if (!$descriptor instanceof Filterable) {
            return $descriptor;
        }

        // filter the descriptor; this may result in the descriptor being removed!
        $descriptor = $this->filter($descriptor);

        return $descriptor;
    }

    public function setApiSpecification(ApiSpecification $apiSpecification): void
    {
        $this->apiSpecification = $apiSpecification;
    }

    public function createApiDocumentationSet(Project $project): void
    {
        $customSettings = $this->getProjectDescriptor()->getSettings()->getCustom();
        foreach ($this->servicesWithCustomSettings as $service) {
            // We assume that the custom settings have the non-default settings and we should not override those;
            // that is why we merge the custom settings on top of the default settings; this will cause the overrides
            // to remain in place.
            $customSettings = array_merge($service->getDefaultSettings(), $customSettings);
        }

        $this->getProjectDescriptor()->getSettings()->setCustom($customSettings);

        foreach ($project->getFiles() as $file) {
            $descriptor = $this->buildDescriptor($file, FileDescriptor::class);
            if ($descriptor === null) {
                continue;
            }

            $this->getProjectDescriptor()->getFiles()->set($descriptor->getPath(), $descriptor);
        }

        $namespaces = $this->getProjectDescriptor()->getIndexes()->fetch('namespaces', new Collection());

        foreach ($project->getNamespaces() as $namespace) {
            $namespaces->set(
                (string) $namespace->getFqsen(),
                $this->buildDescriptor($namespace, NamespaceDescriptor::class)
            );
        }
    }

    public function getDefaultPackage(): string
    {
        return $this->apiSpecification['default-package-name'];
    }

    public function setVisibility(int $visibility): void
    {
        $this->project->getSettings()->setVisibility($visibility);
    }

    public function shouldIncludeSource(): bool
    {
        return $this->apiSpecification['include-source'];
    }

    public function setName(string $title): void
    {
        $this->project->setName($title);
    }

    /**
     * @param Collection<string> $partials
     */
    public function setPartials(Collection $partials): void
    {
        $this->project->setPartials($partials);
    }

    /**
     * @param array<string, string> $customSettings
     */
    public function setCustomSettings(array $customSettings): void
    {
        $this->project->getSettings()->setCustom($customSettings);
    }

    public function addVersion(VersionDescriptor $version): void
    {
        $this->project->getVersions()->add($version);
    }
}
