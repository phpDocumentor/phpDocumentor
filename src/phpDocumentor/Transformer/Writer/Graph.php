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

namespace phpDocumentor\Transformer\Writer;

use phpDocumentor\Descriptor\ApiSetDescriptor;
use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Transformer\Transformation;
use phpDocumentor\Transformer\Writer\Graph\Generator;

use const DIRECTORY_SEPARATOR;

/**
 * Writer responsible for generating various graphs.
 *
 * The Graph writer is capable of generating a Graph (as provided using the 'source' parameter) at the location provided
 * using the artifact parameter.
 *
 * Currently supported:
 *
 * * 'class' (default), a Class Diagram generated using GraphViz
 */
final class Graph extends WriterAbstract implements ProjectDescriptor\WithCustomSettings
{
    use IoTrait;

    public function __construct(
        private readonly Generator $plantumlClassDiagram,
    ) {
    }

    public function getName(): string
    {
        return 'Graph';
    }

    /** @return array<string, bool> */
    public function getDefaultSettings(): array
    {
        return ['graphs.enabled' => false];
    }

    /**
     * Generates a UML class diagram using PlantUML or our native GraphViz integration.
     *
     * @param Transformation $transformation Transformation to execute.
     * @param ProjectDescriptor $project        Document containing the structure.
     */
    public function transform(
        Transformation $transformation,
        ProjectDescriptor $project,
        DocumentationSetDescriptor $documentationSet,
    ): void {
        if ($project->getSettings()->getCustom()['graphs.enabled'] === false) {
            return;
        }

        if ($documentationSet instanceof ApiSetDescriptor === false) {
            return;
        }

        $filename = $this->getDestinationPath($transformation);

        switch ($transformation->getSource() ?: 'class') {
            case 'class':
            default:
                $graph = $this->plantumlClassDiagram->create($documentationSet);
                if ($graph !== null) {
                    $this->persistTo($transformation, $filename, $graph);
                }
        }
    }

    private function getDestinationPath(Transformation $transformation): string
    {
        return $transformation->getTransformer()->getTarget() . DIRECTORY_SEPARATOR . $transformation->getArtifact();
    }
}
