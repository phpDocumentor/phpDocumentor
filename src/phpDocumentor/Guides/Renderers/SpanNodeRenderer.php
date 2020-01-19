<?php

declare(strict_types=1);

/*
 * This file is part of the Docs Builder package.
 * (c) Ryan Weaver <ryan@symfonycasts.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace phpDocumentor\Guides\Renderers;

use Doctrine\RST\Environment;
use Doctrine\RST\HTML\Renderers\SpanNodeRenderer as BaseSpanNodeRenderer;
use Doctrine\RST\Nodes\SpanNode;
use Doctrine\RST\Templates\TemplateRenderer;
use phpDocumentor\Guides\UrlChecker;

class SpanNodeRenderer extends BaseSpanNodeRenderer
{
    /** @var TemplateRenderer */
    private $templateRenderer;
    /** @var UrlChecker|null */
    private $urlChecker;

    public function __construct(
        Environment $environment,
        SpanNode $span,
        TemplateRenderer $templateRenderer,
        ?UrlChecker $urlChecker = null
    ) {
        parent::__construct($environment, $span, $templateRenderer);

        $this->templateRenderer = $templateRenderer;
        $this->urlChecker = $urlChecker;
    }

    /**
     * @param mixed[] $attributes
     */
    public function link(?string $url, string $title, array $attributes = []) : string
    {
        $url = (string) $url;

        if (
            $this->urlChecker &&
            $this->isExternalUrl($url) &&
            false === strpos($url, 'http://localhost') &&
            false === strpos($url, 'http://192.168')
        ) {
            $this->urlChecker->checkUrl($url);
        }

        return $this->templateRenderer->render(
            'link.html.twig',
            [
                'url' => $this->environment->generateUrl($url),
                'title' => $title,
                'attributes' => $attributes,
            ]
        );
    }

    public function isExternalUrl($url) : bool
    {
        return false !== strpos($url, '://');
    }
}
