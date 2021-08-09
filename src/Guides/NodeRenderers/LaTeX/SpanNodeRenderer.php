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

namespace phpDocumentor\Guides\NodeRenderers\LaTeX;

use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\NodeRenderers\SpanNodeRenderer as BaseSpanNodeRenderer;
use phpDocumentor\Guides\References\ResolvedReference;
use phpDocumentor\Guides\Renderer;

use function is_string;
use function substr;
use function trim;

class SpanNodeRenderer extends BaseSpanNodeRenderer
{
    /** @var Renderer */
    private $renderer;

    public function __construct(Environment $environment)
    {
        parent::__construct($environment);

        $this->renderer = $environment->getRenderer();
    }

    public function emphasis(string $text): string
    {
        return $this->renderer->render('emphasis.tex.twig', ['text' => $text]);
    }

    public function strongEmphasis(string $text): string
    {
        return $this->renderer->render('strong-emphasis.tex.twig', ['text' => $text]);
    }

    public function nbsp(): string
    {
        return $this->renderer->render('nbsp.tex.twig');
    }

    public function br(): string
    {
        return $this->renderer->render('br.tex.twig');
    }

    public function literal(string $text): string
    {
        return $this->renderer->render('literal.tex.twig', ['text' => $text]);
    }

    /**
     * @param string[] $attributes
     */
    public function link(?string $url, string $title, array $attributes = []): string
    {
        $type = 'href';

        if (is_string($url) && $url !== '' && $url[0] === '#') {
            $type = 'ref';

            $url = substr($url, 1);
            $url = $url !== '' ? '#' . $url : '';
            $url = $this->environment->getUrl() . $url;
        }

        return $this->renderer->render(
            'link.tex.twig',
            [
                'type' => $type,
                'url' => $url,
                'title' => $title,
                'attributes' => $attributes,
            ]
        );
    }

    public function escape(string $span): string
    {
        return $span;
    }

    /**
     * @param string[] $value
     */
    public function reference(ResolvedReference $reference, array $value): string
    {
        $text = $value['text'] ?: $reference->getTitle();
        $url = $reference->getUrl();

        if ($value['anchor'] !== '') {
            $url .= $value['anchor'];
        }

        if ($text === null) {
            $text = '';
        }

        if ($url === null) {
            $url = '';
        }

        return $this->link($url, trim($text));
    }
}
