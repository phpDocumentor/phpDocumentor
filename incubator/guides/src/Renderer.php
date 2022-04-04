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
use phpDocumentor\Guides\NodeRenderers\FullDocumentNodeRenderer;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Renderer\OutputFormatRenderer;
use phpDocumentor\Guides\Twig\EnvironmentBuilder;
use RuntimeException;

use function sprintf;

class Renderer implements FullDocumentNodeRenderer
{
    /** @var iterable<OutputFormatRenderer> */
    private $outputFormatRenderers;

    /** @var OutputFormatRenderer|null */
    private $outputRenderer;
    private EnvironmentBuilder $environmentBuilder;

    /** @param iterable<OutputFormatRenderer> $outputFormatRenderers */
    public function __construct(iterable $outputFormatRenderers, EnvironmentBuilder $environmentBuilder) {
        $this->outputFormatRenderers = $outputFormatRenderers;
        $this->environmentBuilder = $environmentBuilder;
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

    public function renderNode(Node $node, RenderContext $environment): string
    {
        $this->setOutputRenderer($environment);
        return $this->outputRenderer->render($node, $environment);
    }

    public function renderDocument(DocumentNode $node, RenderContext $environment): string
    {
        $this->setOutputRenderer($environment);
        $this->environmentBuilder->setContext($environment);

        return $this->outputRenderer->renderDocument($node, $environment);
    }

    private function setOutputRenderer(RenderContext $environment): void
    {
        foreach ($this->outputFormatRenderers as $outputFormatRenderer) {
            if (!$outputFormatRenderer->supports($environment->getOutputFormat())) {
                continue;
            }

            $this->outputRenderer = $outputFormatRenderer;
        }

        if ($this->outputRenderer === null) {
            throw new InvalidArgumentException(
                sprintf('Output format "%s" is not supported', $environment->getOutputFormat())
            );
        }
    }
}
