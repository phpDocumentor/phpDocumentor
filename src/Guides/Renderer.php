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

namespace phpDocumentor\Guides;

use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Guides\Twig\AssetsExtension;
use phpDocumentor\Transformer\Transformation;
use phpDocumentor\Transformer\Writer\Twig\EnvironmentFactory;
use RuntimeException;

final class Renderer
{
    /** @var EnvironmentFactory */
    private $environmentFactory;

    /** @var TemplateRenderer|null */
    private $templateRenderer;

    public function __construct(EnvironmentFactory $environmentFactory)
    {
        $this->environmentFactory = $environmentFactory;
    }

    public function initialize(
        ProjectDescriptor $project,
        DocumentationSetDescriptor $documentationSet,
        Transformation $transformation
    ) : void {
        $targetDirectory = $documentationSet->getOutput();

        $environment = $this->environmentFactory->create($project, $transformation, $targetDirectory);
        $environment->addExtension(new AssetsExtension());
        $this->templateRenderer = new TemplateRenderer($environment, 'guides', $targetDirectory);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function render(string $template, array $context = []) : string
    {
        if ($this->templateRenderer === null) {
            throw new RuntimeException('Renderer should be initialized before use');
        }

        return $this->templateRenderer->render($template, $context);
    }

    public function setDestination(string $destination) : void
    {
        $this->templateRenderer->setDestination($destination);
    }
}
