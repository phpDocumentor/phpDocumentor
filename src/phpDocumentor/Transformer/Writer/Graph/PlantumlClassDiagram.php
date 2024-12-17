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
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\Interfaces\NamespaceInterface;
use phpDocumentor\Descriptor\TraitDescriptor;
use phpDocumentor\Guides\Graphs\Renderer\PlantumlRenderer;
use phpDocumentor\Guides\RenderContext;
use phpDocumentor\Uml\ClassDiagram;
use Psr\Log\LoggerInterface;

use function addslashes;
use function implode;

use const PHP_EOL;

final class PlantumlClassDiagram implements Generator
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly PlantumlRenderer $plantumlRenderer,
    ) {
    }

    public function create(DocumentationSetDescriptor $documentationSet): string|null
    {
        if ($documentationSet instanceof ApiSetDescriptor === false) {
            return null;
        }

        $output = $this->plantumlRenderer->render(
            new class extends RenderContext{
                public function __construct()
                {
                }

                public function getLoggerInformation(): array
                {
                    return [];
                }
            },
            (new ClassDiagram())->generateUml([$documentationSet->getNamespace()]),
        );

        if (! $output) {
            $this->logger->error('Generating the class diagram failed');

            return null;
        }

        return $output;
    }
}
