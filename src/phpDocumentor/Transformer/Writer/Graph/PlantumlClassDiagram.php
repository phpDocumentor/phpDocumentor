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

namespace phpDocumentor\Transformer\Writer\Graph;

use phpDocumentor\Descriptor\ApiSetDescriptor;
use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use phpDocumentor\Guides\Graphs\Twig\UmlExtension;
use phpDocumentor\Guides\RenderContext;
use phpDocumentor\Uml\ClassDiagram;

final class PlantumlClassDiagram implements Generator
{
    public function __construct(
        private readonly UmlExtension $plantumlRenderer,
    ) {
    }

    public function create(DocumentationSetDescriptor $documentationSet): string|null
    {
        if ($documentationSet instanceof ApiSetDescriptor === false) {
            return null;
        }

        $output = $this->plantumlRenderer->uml(
            [
                'env' =>
                new class extends RenderContext{
                    public function __construct()
                    {
                    }

                    public function getLoggerInformation(): array
                    {
                        return [];
                    }
                },
            ],
            (new ClassDiagram())->generateUml([$documentationSet->getNamespace()]),
        );

        if (! $output) {
            return null;
        }

        return $output;
    }
}
