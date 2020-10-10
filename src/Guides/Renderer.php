<?php

declare(strict_types=1);

namespace phpDocumentor\Guides;

use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Guides\Twig\AssetsExtension;
use phpDocumentor\Transformer\Transformation;
use phpDocumentor\Transformer\Writer\Twig\EnvironmentFactory;

final class Renderer
{
    /** @var EnvironmentFactory */
    private $environmentFactory;

    /** @var TemplateRenderer|null */
    private $templateRenderer;

    /** @var \League\Flysystem\FilesystemInterface */
    private $destination;

    /** @var string */
    private $targetDirectory;

    public function __construct(EnvironmentFactory $environmentFactory)
    {
        $this->environmentFactory = $environmentFactory;
    }

    public function initialize(
        ProjectDescriptor $project,
        DocumentationSetDescriptor $documentationSet,
        Transformation $transformation
    ) : void {
        $this->targetDirectory = $documentationSet->getOutput();

        $environment = $this->environmentFactory->create($project, $transformation, $this->targetDirectory);
        $environment->addExtension(new AssetsExtension());
        $this->templateRenderer = new TemplateRenderer($environment, 'guides', $this->targetDirectory);

        $this->destination = $transformation->getTransformer()->destination();
    }

    public function render(string $template, array $context = []) : string
    {
        if ($this->templateRenderer === null) {
            throw new \RuntimeException('Renderer should be initialized before use');
        }

        return $this->templateRenderer->render($template, $context);
    }

    public function setDestination(string $destination)
    {
        $this->templateRenderer->setDestination($destination);
    }
}
