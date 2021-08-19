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

use phpDocumentor\Descriptor\ProjectDescriptor\WithCustomSettings;

/**
 * Builds a Project Descriptor and underlying tree.
 */
class ProjectDescriptorBuilder
{
    /** @var string */
    public const DEFAULT_PROJECT_NAME = 'Untitled project';

    /** @var ProjectDescriptor $project */
    protected $project;

    /** @var iterable<WithCustomSettings> */
    private $servicesWithCustomSettings;

    /**
     * @param iterable<WithCustomSettings> $servicesWithCustomSettings
     */
    public function __construct(
        iterable $servicesWithCustomSettings = []
    ) {
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
