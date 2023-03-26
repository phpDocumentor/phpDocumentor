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

namespace phpDocumentor\Transformer\View;

use phpDocumentor\Descriptor\ApiSetDescriptor;
use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Transformer\Transformation;

use function array_merge;
use function count;
use function strlen;
use function strpos;
use function substr;

final class DefaultViewSet implements ViewSet
{
    private ProjectDescriptor $project;
    private DocumentationSetDescriptor $documentationSet;
    private Transformation $transformation;

    public function __construct(
        ProjectDescriptor $project,
        DocumentationSetDescriptor $documentationSet,
        Transformation $transformation
    ) {
        $this->project = $project;
        $this->documentationSet = $documentationSet;
        $this->transformation = $transformation;
    }

    public static function create(
        ProjectDescriptor $project,
        DocumentationSetDescriptor $documentationSet,
        Transformation $transformation
    ): self {
        return new static($project, $documentationSet, $transformation);
    }

    public function getViews(): array
    {
        $extraParameters = [];
        foreach ($this->project->getSettings()->getCustom() as $key => $value) {
            if (strpos($key, 'template.') !== 0) {
                continue;
            }

            $extraParameters[substr($key, strlen('template.'))] = $value;
        }

        $parameters = array_merge($this->transformation->getParameters(), $extraParameters);

        $rootNamespace = $this->documentationSet instanceof ApiSetDescriptor
            ? $this->documentationSet->getNamespace()
            : null;
        $usesNamespaces = $rootNamespace !== null && count($rootNamespace->getChildren()) > 0;

        $rootPackage = $this->documentationSet instanceof ApiSetDescriptor
            ? $this->documentationSet->getPackage()
            : null;
        $usesPackages = $rootPackage !== null && count($rootPackage->getChildren()) > 0;

        return [
            'project' => [
                'name' => $this->project->getName(),
                'title' => $this->project->getName(),
                'settings' => $this->project->getSettings(),
                'versions' => $this->project->getVersions(),
                'files' => $this->documentationSet->getFiles(),
                'indexes' => $this->documentationSet->getIndexes(),
                'package' => $rootPackage,
                'namespace' => $rootNamespace,
            ],
            'documentationSet' => $this->documentationSet,
            'usesNamespaces' => $usesNamespaces,
            'usesPackages' => $usesPackages,
            'parameter' => $parameters,
        ];
    }
}
