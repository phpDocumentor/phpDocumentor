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

use InvalidArgumentException;
use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use phpDocumentor\Descriptor\GuideSetDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Guides\NodeRenderers\FullDocumentNodeRenderer;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Renderer\OutputFormatRenderer;
use phpDocumentor\Guides\Renderer\TemplateRenderer;
use phpDocumentor\Guides\Twig\AssetsExtension;
use phpDocumentor\Transformer\Transformation;
use phpDocumentor\Transformer\Writer\Graph\PlantumlRenderer;
use phpDocumentor\Transformer\Writer\Twig\EnvironmentFactory;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Twig\Environment;
use Webmozart\Assert\Assert;

use function count;
use function sprintf;

class Renderer implements FullDocumentNodeRenderer
{
    /** @var EnvironmentFactory */
    private $twigFactory;

    /** @var Environment|null */
    private $twig;

    /** @var LoggerInterface */
    private $logger;

    /** @var PlantumlRenderer */
    private $plantumlRenderer;

    /** @var iterable<OutputFormatRenderer> */
    private $outputFormatRenderers;

    /** @var OutputFormatRenderer|null */
    private $outputRenderer;

    /** @param iterable<OutputFormatRenderer> $outputFormatRenderers */
    public function __construct(
        EnvironmentFactory $twigFactory,
        LoggerInterface $logger,
        PlantumlRenderer $plantumlRenderer,
        iterable $outputFormatRenderers
    ) {
        $this->twigFactory = $twigFactory;
        $this->logger = $logger;
        $this->plantumlRenderer = $plantumlRenderer;
        $this->outputFormatRenderers = $outputFormatRenderers;
    }

    /** @param DocumentationSetDescriptor|GuideSetDescriptor $documentationSet */
    public function initialize(
        ProjectDescriptor $project,
        DocumentationSetDescriptor $documentationSet,
        Transformation $transformation
    ): void {
        $targetDirectory = $documentationSet->getOutputLocation();
        Assert::isInstanceOf($documentationSet, GuideSetDescriptor::class);

        foreach ($this->outputFormatRenderers as $outputFormatRenderer) {
            if (!$outputFormatRenderer->supports($documentationSet->getOutputFormat())) {
                continue;
            }

            $this->outputRenderer = $outputFormatRenderer;
        }

        if ($this->outputRenderer === null) {
            throw new InvalidArgumentException(
                sprintf('Output format "%s" is not supported', $documentationSet->getOutputFormat())
            );
        }

        $this->twig = $this->twigFactory->create($project, $transformation->template());
        $this->twig->addExtension(new AssetsExtension($this->logger, $this->plantumlRenderer, $this->outputRenderer));
        $this->twig->addGlobal('project', $project);
        $this->twig->addGlobal('usesNamespaces', count($project->getNamespace()->getChildren()) > 0);
        $this->twig->addGlobal('usesPackages', count($project->getPackage()->getChildren()) > 0);
        $this->twig->addGlobal('documentationSet', $documentationSet);
        $this->twig->addGlobal('destinationPath', $targetDirectory);

        // pre-set the global variable so that we can update it later
        $this->twig->addGlobal('env', null);
        $this->twig->addGlobal('destination', $transformation->getTransformer()->destination());

        $this->outputRenderer->setTemplateRenderer(new TemplateRenderer($this->twig, 'guides'));
    }

    /**
     * @param array<string, mixed> $context
     */
    public function render(string $template, array $context = []): string
    {
        if ($this->outputRenderer === null) {
            throw new RuntimeException('Renderer should be initialized before use');
        }

        return $this->outputRenderer->renderTemplate($template, $context);
    }

    public function setDestination(string $destination): void
    {
        $this->outputRenderer->setDestination($destination);
    }

    public function renderDocument(DocumentNode $node, RenderContext $environment): string
    {
        return $this->outputRenderer->renderDocument($node, $environment);
    }

    /**
     * @todo I am not convinced that such a specific solution for Guides would be best since it is used in the Assets
     *   extension; and this extension would actually be nice to re-use in the rest of phpDocumentor as well. First,
     *   make it work here; then adapt for the rest :)
     */
    public function setGuidesEnvironment(RenderContext $environment): void
    {
        $this->twig->addGlobal('env', $environment);
    }
}
