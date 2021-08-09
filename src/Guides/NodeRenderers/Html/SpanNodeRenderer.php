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

namespace phpDocumentor\Guides\NodeRenderers\Html;

use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\NodeRenderers\SpanNodeRenderer as BaseSpanNodeRenderer;
use phpDocumentor\Guides\References\ResolvedReference;
use phpDocumentor\Guides\Renderer;

use function htmlspecialchars;
use function trim;

class SpanNodeRenderer extends BaseSpanNodeRenderer
{
    /** @var Renderer */
    private $renderer;

    public function __construct(Environment $environment)
    {
        $this->renderer = $environment->getRenderer();

        parent::__construct($environment);
    }

    public function emphasis(string $text): string
    {
        return $this->renderer->render('emphasis.html.twig', ['text' => $text]);
    }

    public function strongEmphasis(string $text): string
    {
        return $this->renderer->render('strong-emphasis.html.twig', ['text' => $text]);
    }

    public function nbsp(): string
    {
        return 'nbsp;';

        // TODO: this is called in DocumentNode's getTitle function during parsing; wtf?
        // return $this->renderer->render('nbsp.html.twig');
    }

    public function br(): string
    {
        return '<br>';

        // TODO: this is called in DocumentNode's getTitle function during parsing; wtf?
        // return $this->renderer->render('br.html.twig');
    }

    public function literal(string $text): string
    {
        return $this->renderer->render('literal.html.twig', ['text' => $text]);
    }

    /**
     * @param string[] $attributes
     */
    public function link(?string $url, string $title, array $attributes = []): string
    {
        $url = (string) $url;

        return $this->renderer->render(
            'link.html.twig',
            [
                'url' => $this->environment->generateUrl($url),
                'title' => $title,
                'attributes' => $attributes,
            ]
        );
    }

    public function escape(string $span): string
    {
        return htmlspecialchars($span);
    }

    /**
     * @param array<string|null> $value
     */
    public function reference(ResolvedReference $reference, array $value): string
    {
        $text = $value['text'] ?: ($reference->getTitle() ?? '');
        $text = trim($text);

        // reference to another document
        if ($reference->getUrl() !== null) {
            $url = $reference->getUrl();

            if ($value['anchor'] !== null) {
                $url .= '#' . $value['anchor'];
            }

            $link = $this->link($url, $text, $reference->getAttributes());

            // reference to anchor in existing document
        } elseif ($value['url'] !== null) {
            $url = $this->environment->getLink($value['url']);

            $link = $this->link($url, $text, $reference->getAttributes());
        } else {
            $link = $this->link('#', $text . ' (unresolved reference)', $reference->getAttributes());
        }

        return $link;
    }
}
