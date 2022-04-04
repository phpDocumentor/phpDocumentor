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

namespace phpDocumentor\Guides\NodeRenderers;

use InvalidArgumentException;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\SpanNode;
use phpDocumentor\Guides\References\ReferenceResolver;
use phpDocumentor\Guides\RenderContext;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Guides\Span\CrossReferenceNode;
use phpDocumentor\Guides\Span\LiteralToken;
use phpDocumentor\Guides\Span\SpanToken;
use phpDocumentor\Guides\UrlGenerator;
use Psr\Log\LoggerInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

use function assert;
use function is_string;
use function preg_replace;
use function preg_replace_callback;
use function sprintf;
use function str_replace;

abstract class SpanNodeRenderer implements NodeRenderer, SpanRenderer, NodeRendererFactoryAware
{
    /** @var Renderer */
    protected $renderer;

    /** @var NodeRendererFactory */
    private $nodeRendererFactory;

    /** @var ReferenceResolver */
    private $referenceResolver;

    private LoggerInterface $logger;
    protected UrlGenerator $urlGenerator;

    public function __construct(
        Renderer $renderer,
        ReferenceResolver $referenceResolver,
        LoggerInterface $logger,
        UrlGenerator $urlGenerator
    ) {
        $this->renderer = $renderer;
        $this->referenceResolver = $referenceResolver;
        $this->logger = $logger;
        $this->urlGenerator = $urlGenerator;
    }

    public function setNodeRendererFactory(NodeRendererFactory $nodeRendererFactory): void
    {
        $this->nodeRendererFactory = $nodeRendererFactory;
    }

    public function render(Node $node, RenderContext $environment): string
    {
        if ($node instanceof SpanNode === false) {
            throw new InvalidArgumentException('Invalid node presented');
        }

        $value = $node->getValue();

        $span = $this->renderSyntaxes($value, $environment);

        $span = $this->renderTokens($node, $span, $environment);

        return $span;
    }

    /**
     * @param string[] $attributes
     */
    public function link(RenderContext $environment, ?string $url, string $title, array $attributes = []): string
    {
        $url = (string) $url;

        return $this->renderer->render(
            'link.html.twig',
            [
                'url' => $this->urlGenerator->generateUrl($url),
                'title' => $title,
                'attributes' => $attributes,
            ]
        );
    }

    private function renderSyntaxes(string $span, RenderContext $environment): string
    {
        $span = $this->escape($span);

        $span = $this->renderStrongEmphasis($span);

        $span = $this->renderEmphasis($span);

        $span = $this->renderNbsp($span);

        $span = $this->renderVariables($span, $environment);

        $span = $this->renderBrs($span);

        return $span;
    }

    private function renderStrongEmphasis(string $span): string
    {
        return preg_replace_callback(
            '/\*\*(.+)\*\*/mUsi',
            function (array $matches): string {
                return $this->strongEmphasis($matches[1]);
            },
            $span
        );
    }

    private function renderEmphasis(string $span): string
    {
        return preg_replace_callback(
            '/\*(.+)\*/mUsi',
            function (array $matches): string {
                return $this->emphasis($matches[1]);
            },
            $span
        );
    }

    private function renderNbsp(string $span): string
    {
        return preg_replace('/~/', $this->nbsp(), $span);
    }

    private function renderVariables(string $span, RenderContext $environment): string
    {
        return preg_replace_callback(
            '/\|(.+)\|/mUsi',
            function (array $match) use ($environment): string {
                $variable = $environment->getVariable($match[1]);

                if ($variable === null) {
                    return '';
                }

                if ($variable instanceof Node) {
                    return $this->nodeRendererFactory->get($variable)->render($variable, $environment);
                }

                if (is_string($variable)) {
                    return $variable;
                }

                return (string) $variable;
            },
            $span
        );
    }

    private function renderBrs(string $span): string
    {
        // Adding brs when a space is at the end of a line
        return preg_replace('/ \n/', $this->br(), $span);
    }

    private function renderTokens(SpanNode $node, string $span, RenderContext $environment): string
    {
        foreach ($node->getTokens() as $token) {
            if ($token instanceof CrossReferenceNode) {
                $reference = $this->referenceResolver->resolve($token, $environment);

                if ($reference === null) {
                    $this->logger->error(sprintf('Invalid cross reference: %s', $token->getUrl()));

                    $span = str_replace($token->getId(), $token->getText(), $span);
                    continue;
                }

                $span = str_replace(
                    $token->getId(),
                    $this->link($environment, $reference->getUrl(), $reference->getTitle(), $reference->getAttributes()),
                    $span
                );

                continue;
            }

            $span = $this->renderToken($token, $span, $environment);
        }

        return $span;
    }

    private function renderToken(SpanToken $spanToken, string $span, RenderContext $environment): string
    {
        switch ($spanToken->getType()) {
            case SpanToken::TYPE_LITERAL:
                assert($spanToken instanceof LiteralToken);

                return $this->renderLiteral($spanToken, $span);

            case SpanToken::TYPE_LINK:
                return $this->renderLink($spanToken, $span, $environment);
        }

        throw new InvalidArgumentException(sprintf('Unknown token type %s', $spanToken->getType()));
    }

    private function renderLiteral(LiteralToken $token, string $span): string
    {
        return str_replace(
            $token->getId(),
            $this->literal($token),
            $span
        );
    }

    private function renderLink(SpanToken $spanToken, string $span, RenderContext $environment): string
    {
        $url = $spanToken->get('url');
        $link = $spanToken->get('link');

        if ($url === '') {
            $url = $environment->getLink($link);

            if ($url === '') {
                $metaEntry = $environment->getMetaEntry();

                if ($metaEntry !== null && $metaEntry->hasTitle($link)) {
                    $url = $environment->relativeDocUrl(
                        $metaEntry->getUrl(),
                        (new AsciiSlugger())->slug($link)->lower()->toString()
                    );
                }
            }

            if ($url === '') {
                $this->logger->error(sprintf('Invalid link: %s', $link));

                return str_replace($spanToken->getId(), $link, $span);
            }
        }

        $link = $this->link($environment, $url, $this->renderSyntaxes($link, $environment));

        return str_replace($spanToken->getId(), $link, $span);
    }
}
