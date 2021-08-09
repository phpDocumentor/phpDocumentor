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
use phpDocumentor\Transformer\Writer\Graph\PlantumlRenderer;
use phpDocumentor\Transformer\Writer\Twig\EnvironmentFactory;
use Psr\Log\LoggerInterface;
use RuntimeException;

use function count;

class Renderer
{
    /** @var EnvironmentFactory */
    private $environmentFactory;

    /** @var TemplateRenderer|null */
    private $templateRenderer;

    /** @var \Twig\Environment|null */
    private $environment;

    /** @var LoggerInterface */
    private $logger;

    /** @var PlantumlRenderer */
    private $plantumlRenderer;

    public function __construct(
        EnvironmentFactory $environmentFactory,
        LoggerInterface $logger,
        PlantumlRenderer $plantumlRenderer
    ) {
        $this->environmentFactory = $environmentFactory;
        $this->logger = $logger;
        $this->plantumlRenderer = $plantumlRenderer;
    }

    public function initialize(
        ProjectDescriptor $project,
        DocumentationSetDescriptor $documentationSet,
        Transformation $transformation
    ): void {
        $targetDirectory = $documentationSet->getOutput();

        $this->environment = $this->environmentFactory->create($project, $transformation->template());
        $this->environment->addExtension(new AssetsExtension($this->logger, $this->plantumlRenderer));
        $this->environment->addGlobal('project', $project);
        $this->environment->addGlobal('usesNamespaces', count($project->getNamespace()->getChildren()) > 0);
        $this->environment->addGlobal('usesPackages', count($project->getPackage()->getChildren()) > 0);
        $this->environment->addGlobal('documentationSet', $project);
        $this->environment->addGlobal('destinationPath', $targetDirectory);

        // pre-set the global variable so that we can update it later
        $this->environment->addGlobal('env', null);
        $this->environment->addGlobal('destination', $transformation->getTransformer()->destination());

        $this->templateRenderer = new TemplateRenderer($this->environment, 'guides', $targetDirectory);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function render(string $template, array $context = []): string
    {
        if ($this->templateRenderer === null) {
            throw new RuntimeException('Renderer should be initialized before use');
        }

        return $this->templateRenderer->render($template, $context);
    }

    public function setDestination(string $destination): void
    {
        $this->templateRenderer->setDestination($destination);
    }

    /**
     * @todo I am not convinced that such a specific solution for Guides would be best since it is used in the Assets
     *   extension; and this extension would actually be nice to re-use in the rest of phpDocumentor as well. First,
     *   make it work here; then adapt for the rest :)
     */
    public function setGuidesEnvironment(Environment $environment): void
    {
        $this->environment->addGlobal('env', $environment);
    }
}
