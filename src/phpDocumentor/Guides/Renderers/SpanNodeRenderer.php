<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 * @author Ryan Weaver <ryan@symfonycasts.com> on the original DocBuilder.
 * @author Mike van Riel <me@mikevanriel.com> for adapting this to phpDocumentor.
 */

namespace phpDocumentor\Guides\Renderers;

use Doctrine\RST\Environment;
use Doctrine\RST\HTML\Renderers\SpanNodeRenderer as BaseSpanNodeRenderer;
use Doctrine\RST\Nodes\SpanNode;
use Doctrine\RST\Templates\TemplateRenderer;

class SpanNodeRenderer extends BaseSpanNodeRenderer
{
    /** @var TemplateRenderer */
    private $templateRenderer;

    public function __construct(
        Environment $environment,
        SpanNode $span,
        TemplateRenderer $templateRenderer
    ) {
        parent::__construct($environment, $span, $templateRenderer);

        $this->templateRenderer = $templateRenderer;
    }

    /**
     * @param string[] $attributes
     */
    public function link(?string $url, string $title, array $attributes = []) : string
    {
        $url = (string) $url;

        return $this->templateRenderer->render(
            'link.html.twig',
            [
                'url' => $this->environment->generateUrl($url),
                'title' => $title,
                'attributes' => $attributes,
            ]
        );
    }
}
