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

namespace phpDocumentor\Transformer\Writer\Twig\LinkRenderer;

use phpDocumentor\Transformer\Writer\Twig\LinkRenderer;

use function count;
use function end;
use function explode;

/**
 * The target of a link and how it should be presented.
 *
 * Links in phpDocumentor can be based on any number of types of input, ranging from FQCNs, Descriptors, absolute urls
 * to things like references.
 *
 * To simplify the rendering of these links, we first analyze and transform the input into this Target object. Once the
 * object is formed, it is passed onto the {@see LinkAdapter} to transform it into an actual HTML link that can be
 * shown.
 */
final class Target
{
    /**
     * Targets can optionally have their link text be an abbreviation.
     *
     * When this field is not null, it is used as the link text and the regular title is set as the title attribute
     * of a generated ABBR tag.
     */
    private string|null $abbreviation;

    /**
     * How should the HTML be formatted.
     *
     * The {@see HtmlFormatter} is responsible for transforming this target into HTML; but the presentation of a link
     * can vary depending on where it is used. When used in twig templates, the caller can indicate how an element
     * should be rendered according to this presentation.
     *
     * @see LinkRenderer for the possible presentation options.
     */
    private string $presentation;

    public function __construct(
        /**
         * The title of the target that needs to be shown in the link text, or when an abbreviation is present
         * as part of the ABBR tag's title attribute.
         */
        private readonly string $title,
        /**
         * The link url of this Target.
         *
         * The {@see LinkRenderer} is responsible for converting an 'input' to something that can be linked to;
         * and the URL represents the thing that should be linked to.
         *
         * Sometimes, input cannot be resolved (FQCNs that we do not document). When that happens the URL is null,
         * and the {@see HtmlFormatter} will still render a reference to that FQCN, but without a link.
         */
        private readonly string|null $url = null,
        string $presentation = LinkRenderer::PRESENTATION_NORMAL,
    ) {
        $this->setPresentation($presentation);
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getAbbreviation(): string|null
    {
        return $this->abbreviation;
    }

    public function getUrl(): string|null
    {
        return $this->url;
    }

    private function setPresentation(string $presentation): void
    {
        $this->presentation = $presentation;

        $this->abbreviation = null;
        switch ($presentation) {
            case LinkRenderer::PRESENTATION_NONE:
            case LinkRenderer::PRESENTATION_URL:
                break;
            case LinkRenderer::PRESENTATION_NORMAL:
            case LinkRenderer::PRESENTATION_CLASS_SHORT:
                $parts = explode('\\', $this->title);
                if (count($parts) > 1) {
                    $this->abbreviation = end($parts);
                }

                break;
            case LinkRenderer::PRESENTATION_FILE_SHORT:
                $parts = explode('/', $this->title);
                if (count($parts) > 1) {
                    $this->abbreviation = end($parts);
                }

                break;
            default:
                $this->abbreviation = $presentation;
                break;
        }
    }

    public function getPresentation(): string
    {
        return $this->presentation;
    }
}
