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
use phpDocumentor\Guides\Renderer\TemplateRenderer;
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
    private $twigFactory;

    /** @var TemplateRenderer|null */
    private $templateRenderer;

    /** @var \Twig\Environment|null */
    private $twig;

    /** @var LoggerInterface */
    private $logger;

    /** @var PlantumlRenderer */
    private $plantumlRenderer;

    public function __construct(
        EnvironmentFactory $twigFactory,
        LoggerInterface $logger,
        PlantumlRenderer $plantumlRenderer
    ) {
        $this->twigFactory = $twigFactory;
        $this->logger = $logger;
        $this->plantumlRenderer = $plantumlRenderer;
    }

    public function initialize(
        ProjectDescriptor $project,
        DocumentationSetDescriptor $documentationSet,
        Transformation $transformation
    ): void {
        $targetDirectory = $documentationSet->getOutputLocation();

        $this->twig = $this->twigFactory->create($project, $transformation->template());
        $this->twig->addExtension(new AssetsExtension($this->logger, $this->plantumlRenderer));
        $this->twig->addGlobal('project', $project);
        $this->twig->addGlobal('usesNamespaces', count($project->getNamespace()->getChildren()) > 0);
        $this->twig->addGlobal('usesPackages', count($project->getPackage()->getChildren()) > 0);
        $this->twig->addGlobal('documentationSet', $documentationSet);
        $this->twig->addGlobal('destinationPath', $targetDirectory);

        // pre-set the global variable so that we can update it later
        $this->twig->addGlobal('env', null);
        $this->twig->addGlobal('destination', $transformation->getTransformer()->destination());

        $this->templateRenderer = new TemplateRenderer($this->twig, 'guides');
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
        $this->twig->addGlobal('env', $environment);
    }
}
