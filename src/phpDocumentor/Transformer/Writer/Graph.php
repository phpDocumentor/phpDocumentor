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

use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Transformer\Transformation;
use phpDocumentor\Transformer\Writer\Graph\GraphVizClassDiagram;
use phpDocumentor\Transformer\Writer\Graph\PlantumlClassDiagram;
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
    /** @var GraphVizClassDiagram */
    private $classDiagramGenerator;

    /** @var PlantumlClassDiagram */
    private $plantumlClassDiagram;

    public function __construct(GraphVizClassDiagram $classDiagramGenerator, PlantumlClassDiagram $plantumlClassDiagram)
    {
        $this->classDiagramGenerator = $classDiagramGenerator;
        $this->plantumlClassDiagram = $plantumlClassDiagram;
    }

    /**
     * @return array<string, bool>
     */
    public function getDefaultSettings() : array
    {
        return [ 'graphs.enabled' => false ];
    }

    /**
     * Generates a UML class diagram using PlantUML or our native GraphViz integration.
     *
     * @param ProjectDescriptor $project Document containing the structure.
     * @param Transformation $transformation Transformation to execute.
     */
    public function transform(ProjectDescriptor $project, Transformation $transformation) : void
    {
        if ($project->getSettings()->getCustom()['graphs.enabled'] === false) {
            return;
        }

        $filename = $this->getDestinationPath($transformation);

        switch ($transformation->getSource() ?: 'class') {
            case 'class':
            default:
                $this->classDiagramGenerator->create($project, $filename);
                $this->plantumlClassDiagram->create($project, $filename);
        }
    }

    private function getDestinationPath(Transformation $transformation) : string
    {
        return $transformation->getTransformer()->getTarget() . DIRECTORY_SEPARATOR . $transformation->getArtifact();
    }
}
