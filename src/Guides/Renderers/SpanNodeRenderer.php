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

namespace phpDocumentor\Guides\Renderers;

use InvalidArgumentException;
use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\InvalidLink;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\SpanNode;
use phpDocumentor\Guides\RestructuredText\Span\SpanToken;
use function is_string;
use function preg_replace;
use function preg_replace_callback;
use function sprintf;
use function str_replace;

abstract class SpanNodeRenderer implements NodeRenderer, SpanRenderer
{
    /** @var Environment */
    protected $environment;

    /** @var SpanNode */
    protected $span;

    public function __construct(
        Environment $environment,
        SpanNode $span
    ) {
        $this->environment = $environment;
        $this->span = $span;
    }

    public function render() : string
    {
        $value = $this->span->getValue();

        $span = $this->renderSyntaxes($value);

        $span = $this->renderTokens($span);

        return $span;
    }

    /**
     * @param string[] $attributes
     */
    public function link(?string $url, string $title, array $attributes = []) : string
    {
        $url = (string) $url;

        return $this->environment->getRenderer()->render(
            'link.html.twig',
            [
                'url' => $this->environment->generateUrl($url),
                'title' => $title,
                'attributes' => $attributes,
            ]
        );
    }

    private function renderSyntaxes(string $span) : string
    {
        $span = $this->escape($span);

        $span = $this->renderStrongEmphasis($span);

        $span = $this->renderEmphasis($span);

        $span = $this->renderNbsp($span);

        $span = $this->renderVariables($span);

        $span = $this->renderBrs($span);

        return $span;
    }

    private function renderStrongEmphasis(string $span) : string
    {
        return preg_replace_callback(
            '/\*\*(.+)\*\*/mUsi',
            function (array $matches) : string {
                return $this->strongEmphasis($matches[1]);
            },
            $span
        );
    }

    private function renderEmphasis(string $span) : string
    {
        return preg_replace_callback(
            '/\*(.+)\*/mUsi',
            function (array $matches) : string {
                return $this->emphasis($matches[1]);
            },
            $span
        );
    }

    private function renderNbsp(string $span) : string
    {
        return preg_replace('/~/', $this->nbsp(), $span);
    }

    private function renderVariables(string $span) : string
    {
        return preg_replace_callback(
            '/\|(.+)\|/mUsi',
            function (array $match) : string {
                $variable = $this->environment->getVariable($match[1]);

                if ($variable === null) {
                    return '';
                }

                if ($variable instanceof Node) {
                    return $variable->render();
                }

                if (is_string($variable)) {
                    return $variable;
                }

                return (string) $variable;
            },
            $span
        );
    }

    private function renderBrs(string $span) : string
    {
        // Adding brs when a space is at the end of a line
        return preg_replace('/ \n/', $this->br(), $span);
    }

    private function renderTokens(string $span) : string
    {
        foreach ($this->span->getTokens() as $token) {
            $span = $this->renderToken($token, $span);
        }

        return $span;
    }

    private function renderToken(SpanToken $spanToken, string $span) : string
    {
        switch ($spanToken->getType()) {
            case SpanToken::TYPE_LITERAL:
                return $this->renderLiteral($spanToken, $span);
            case SpanToken::TYPE_REFERENCE:
                return $this->renderReference($spanToken, $span);
            case SpanToken::TYPE_LINK:
                return $this->renderLink($spanToken, $span);
        }

        throw new InvalidArgumentException(sprintf('Unknown token type %s', $spanToken->getType()));
    }

    private function renderLiteral(SpanToken $spanToken, string $span) : string
    {
        return str_replace(
            $spanToken->getId(),
            $this->literal($spanToken->get('text')),
            $span
        );
    }

    private function renderReference(SpanToken $spanToken, string $span) : string
    {
        $reference = $this->environment->resolve($spanToken->get('section'), $spanToken->get('url'));

        if ($reference === null) {
            $this->environment->addInvalidLink(new InvalidLink($spanToken->get('url')));

            return str_replace($spanToken->getId(), $spanToken->get('text'), $span);
        }

        $link = $this->reference($reference, $spanToken->getTokenData());

        return str_replace($spanToken->getId(), $link, $span);
    }

    private function renderLink(SpanToken $spanToken, string $span) : string
    {
        $url = $spanToken->get('url');
        $link = $spanToken->get('link');

        if ($url === '') {
            $url = $this->environment->getLink($link);

            if ($url === '') {
                $metaEntry = $this->environment->getMetaEntry();

                if ($metaEntry !== null && $metaEntry->hasTitle($link)) {
                    $url = $metaEntry->getUrl() . '#' . Environment::slugify($link);
                }
            }

            if ($url === '') {
                $this->environment->addInvalidLink(new InvalidLink($link));

                return str_replace($spanToken->getId(), $link, $span);
            }
        }

        $link = $this->link($url, $this->renderSyntaxes($link));

        return str_replace($spanToken->getId(), $link, $span);
    }
}
