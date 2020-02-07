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

namespace phpDocumentor\Descriptor;

use InvalidArgumentException;
use phpDocumentor\Descriptor\Builder\AssemblerFactory;
use phpDocumentor\Descriptor\Builder\AssemblerInterface;
use phpDocumentor\Descriptor\Filter\Filter;
use phpDocumentor\Descriptor\Filter\Filterable;
use phpDocumentor\Descriptor\ProjectDescriptor\WithCustomSettings;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Php\Argument;
use phpDocumentor\Reflection\Php\File;
use phpDocumentor\Reflection\Php\Project;
use RuntimeException;
use function array_merge;
use function get_class;
use function sprintf;

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

    /** @var string */
    private $defaultPackage = '';

    /** @var iterable<WithCustomSettings> */
    private $servicesWithCustomSettings;

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

    public function createProjectDescriptor() : void
    {
        $this->project = new ProjectDescriptor(self::DEFAULT_PROJECT_NAME);
    }

    /**
     * Returns the project descriptor that is being built.
     */
    public function getProjectDescriptor() : ProjectDescriptor
    {
        return $this->project;
    }

    /**
     * Takes the given data and attempts to build a Descriptor from it.
     *
     * @param Element|File|Tag|Argument $data
     *
     * @return DescriptorAbstract|Collection<DescriptorAbstract>|null
     *
     * @throws InvalidArgumentException If no Assembler could be found that matches the given data.
     */
    public function buildDescriptor($data)
    {
        $assembler = $this->getAssembler($data);
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
        $descriptor = $assembler->create($data);

        if ($descriptor instanceof Descriptor) {
            return $this->filterDescriptor($descriptor);
        }

        return $this->filterEachDescriptor($descriptor);
    }

    /**
     * Attempts to find an assembler matching the given data.
     *
     * @param mixed $data
     */
    public function getAssembler($data) : ?AssemblerInterface
    {
        return $this->assemblerFactory->get($data);
    }

    /**
     * Analyzes a Descriptor and alters its state based on its state or even removes the descriptor.
     */
    public function filter(Filterable $descriptor) : ?Filterable
    {
        return $this->filter->filter($descriptor);
    }

    /**
     * Filters each descriptor, validates them, stores the validation results and returns a collection of transmuted
     * objects.
     *
     * @param Collection<DescriptorAbstract> $descriptor
     *
     * @return Collection<DescriptorAbstract>
     */
    private function filterEachDescriptor(iterable $descriptor) : Collection
    {
        $descriptors = Collection::fromClassString(DescriptorAbstract::class);
        foreach ($descriptor as $key => $item) {
            $item = $this->filterDescriptor($item);
            if (!$item) {
                continue;
            }

            $descriptors[$key] = $item;
        }

        return $descriptors;
    }

    /**
     * Filters a descriptor, validates it, stores the validation results and returns the transmuted object or null
     * if it is supposed to be removed.
     */
    protected function filterDescriptor(Descriptor $descriptor) : ?Descriptor
    {
        if (!$descriptor instanceof Filterable) {
            return $descriptor;
        }

        // filter the descriptor; this may result in the descriptor being removed!
        $descriptor = $this->filter($descriptor);
        if (!$descriptor instanceof Descriptor) {
            return null;
        }

        return $descriptor;
    }

    public function build(Project $project) : void
    {
        $packageName = $project->getRootNamespace()->getFqsen()->getName();
        $this->defaultPackage = $packageName;

        $customSettings = $this->getProjectDescriptor()->getSettings()->getCustom();
        foreach ($this->servicesWithCustomSettings as $service) {
            // We assume that the custom settings have the non-default settings and we should not override those;
            // that is why we merge the custom settings on top of the default settings; this will cause the overrides
            // to remain in place.
            $customSettings = array_merge($service->getDefaultSettings(), $customSettings);
        }

        $this->getProjectDescriptor()->getSettings()->setCustom($customSettings);

        foreach ($project->getFiles() as $file) {
            $descriptor = $this->buildDescriptor($file);
            if (!$descriptor instanceof FileDescriptor) {
                continue;
            }

            $this->getProjectDescriptor()->getFiles()->set($descriptor->getPath(), $descriptor);
        }

        $namespaces = $this->getProjectDescriptor()->getIndexes()->get('namespaces', new Collection());

        foreach ($project->getNamespaces() as $namespace) {
            $namespaces->set((string) $namespace->getFqsen(), $this->buildDescriptor($namespace));
        }
    }

    public function getDefaultPackage() : string
    {
        return $this->defaultPackage;
    }

    /**
     * @param array<string, array<string>> $apiConfig
     */
    public function setVisibility(array $apiConfig) : void
    {
        $visibilities = $apiConfig['visibility'];
        $visibility = 0;

        foreach ($visibilities as $item) {
            switch ($item) {
                case 'api':
                    $visibility |= ProjectDescriptor\Settings::VISIBILITY_API;
                    break;
                case 'public':
                    $visibility |= ProjectDescriptor\Settings::VISIBILITY_PUBLIC;
                    break;
                case 'protected':
                    $visibility |= ProjectDescriptor\Settings::VISIBILITY_PROTECTED;
                    break;
                case 'private':
                    $visibility |= ProjectDescriptor\Settings::VISIBILITY_PRIVATE;
                    break;
                case 'internal':
                    $visibility |= ProjectDescriptor\Settings::VISIBILITY_INTERNAL;
                    break;
                default:
                    throw new RuntimeException(
                        sprintf(
                            '%s is not a type of visibility, supported is: api, public, protected, private or internal',
                            $item
                        )
                    );
            }
        }

        $this->project->getSettings()->setVisibility($visibility);
    }

    public function setName(string $title) : void
    {
        $this->project->setName($title);
    }

    /**
     * @param Collection<string> $partials
     */
    public function setPartials(Collection $partials) : void
    {
        $this->project->setPartials($partials);
    }

    /**
     * @param array<string> $markers
     */
    public function setMarkers(array $markers) : void
    {
        $this->project->getSettings()->setMarkers($markers);
    }

    /**
     * @param array<string, string> $customSettings
     */
    public function setCustomSettings(array $customSettings) : void
    {
        $this->project->getSettings()->setCustom($customSettings);
    }

    public function setIncludeSource(bool $includeSources) : void
    {
        if ($includeSources) {
            $this->project->getSettings()->includeSource();
        } else {
            $this->project->getSettings()->excludeSource();
        }
    }

    public function addVersion(VersionDescriptor $version) : void
    {
        $this->project->getVersions()->add($version);
    }
}
