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
use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\InvalidLink;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\SpanNode;
use phpDocumentor\Guides\ReferenceBuilder;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Guides\Span\SpanToken;
use Symfony\Component\String\Slugger\AsciiSlugger;

use function get_class;
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

    /** @var ReferenceBuilder */
    private $referenceRegistry;

    public function __construct(Renderer $renderer, ReferenceBuilder $referenceRegistry)
    {
        $this->renderer = $renderer;
        $this->referenceRegistry = $referenceRegistry;
    }

    public function setNodeRendererFactory(NodeRendererFactory $nodeRendererFactory): void
    {
        $this->nodeRendererFactory = $nodeRendererFactory;
    }

    public function render(Node $node, Environment $environment): string
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
    public function link(Environment $environment, ?string $url, string $title, array $attributes = []): string
    {
        $url = (string) $url;

        return $this->renderer->render(
            'link.html.twig',
            [
                'url' => $environment->generateUrl($url),
                'title' => $title,
                'attributes' => $attributes,
            ]
        );
    }

    private function renderSyntaxes(string $span, Environment $environment): string
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

    private function renderVariables(string $span, Environment $environment): string
    {
        return preg_replace_callback(
            '/\|(.+)\|/mUsi',
            function (array $match) use ($environment): string {
                $variable = $environment->getVariable($match[1]);

                if ($variable === null) {
                    return '';
                }

                if ($variable instanceof Node) {
                    return $this->nodeRendererFactory->get(get_class($variable))->render($variable, $environment);
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

    private function renderTokens(SpanNode $node, string $span, Environment $environment): string
    {
        foreach ($node->getTokens() as $token) {
            $span = $this->renderToken($token, $span, $environment);
        }

        return $span;
    }

    private function renderToken(SpanToken $spanToken, string $span, Environment $environment): string
    {
        switch ($spanToken->getType()) {
            case SpanToken::TYPE_LITERAL:
                return $this->renderLiteral($spanToken, $span);

            case SpanToken::TYPE_REFERENCE:
                return $this->renderReference($spanToken, $span, $environment);

            case SpanToken::TYPE_LINK:
                return $this->renderLink($spanToken, $span, $environment);
        }

        throw new InvalidArgumentException(sprintf('Unknown token type %s', $spanToken->getType()));
    }

    private function renderLiteral(SpanToken $spanToken, string $span): string
    {
        return str_replace(
            $spanToken->getId(),
            $this->literal($spanToken->get('text')),
            $span
        );
    }

    private function renderReference(SpanToken $spanToken, string $span, Environment $environment): string
    {
        $role = $spanToken->get('section');
        if ($spanToken->get('domain')) {
            $role = $spanToken->get('domain') . ':' . $role;
        }

        $reference = $this->referenceRegistry->resolve(
            $environment,
            $role,
            $spanToken->get('url'),
            $environment->getMetaEntry()
        );

        if ($reference === null) {
            $this->referenceRegistry->addInvalidLink(new InvalidLink($spanToken->get('url')));

            return str_replace($spanToken->getId(), $spanToken->get('text'), $span);
        }

        $link = $this->reference($environment, $reference, $spanToken->getTokenData());

        return str_replace($spanToken->getId(), $link, $span);
    }

    private function renderLink(SpanToken $spanToken, string $span, Environment $environment): string
    {
        $url = $spanToken->get('url');
        $link = $spanToken->get('link');

        if ($url === '') {
            $url = $environment->getLink($link);

            if ($url === '') {
                $metaEntry = $environment->getMetaEntry();

                if ($metaEntry !== null && $metaEntry->hasTitle($link)) {
                    $url = $metaEntry->getUrl() . '#' . (new AsciiSlugger())->slug($link)->lower()->toString();
                }
            }

            if ($url === '') {
                $this->referenceRegistry->addInvalidLink(new InvalidLink($link));

                return str_replace($spanToken->getId(), $link, $span);
            }
        }

        $link = $this->link($environment, $url, $this->renderSyntaxes($link, $environment));

        return str_replace($spanToken->getId(), $link, $span);
    }
}
