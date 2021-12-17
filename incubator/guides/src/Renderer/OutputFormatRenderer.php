<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Renderer;

use InvalidArgumentException;
use phpDocumentor\Guides\NodeRenderers\FullDocumentNodeRenderer;
use phpDocumentor\Guides\NodeRenderers\NodeRendererFactory;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RenderContext;

class OutputFormatRenderer
{
    /** @var NodeRendererFactory */
    private $nodeRendererFactory;

    /** @var string */
    private $format;

    /** @var TemplateRenderer */
    private $templateRenderer;

    public function __construct(string $format, NodeRendererFactory $nodeRendererFactory)
    {
        $this->format = $format;
        $this->nodeRendererFactory = $nodeRendererFactory;
    }

    public function supports(string $format): bool
    {
        return $this->format === $format;
    }

    /**
     * @TODO: can we remove this, once the rendering is less spaghetti? Template rendering is something that
     *  belongs to a particular output format. But as our current twig logic needs extra variables we are forced
     *  to inject the template renderer here. As it cannot be autowired.
     */
    public function setTemplateRenderer(TemplateRenderer $templateRenderer): void
    {
        $this->templateRenderer = $templateRenderer;
    }

    /**
     * Our noderenderes are consuming this method. It should only be called by them. Nobody else!
     *
     * @param array<string, mixed> $context
     */
    public function renderTemplate(string $template, array $context): string
    {
        return $this->templateRenderer->render($template, $context);
    }

    public function setDestination(string $destination): void
    {
        $this->templateRenderer->setDestination($destination);
    }

    public function render(Node $node, RenderContext $environment): string
    {
        return $this->nodeRendererFactory->get($node)->render($node, $environment);
    }

    public function renderDocument(DocumentNode $node, RenderContext $environment): string
    {
        $renderer = $this->nodeRendererFactory->get($node);
        if ($renderer instanceof FullDocumentNodeRenderer === false) {
            throw new InvalidArgumentException('Expected FullDocumentNodeRenderer not found');
        }

        return $renderer->renderDocument($node, $environment);
    }
}
