<?php

declare(strict_types=1);

namespace phpDocumentor\Transformer\Writer\LinkRenderer;

use phpDocumentor\Transformer\Writer\Twig\LinkRenderer;

use function count;
use function end;
use function explode;
use function sprintf;

final class HtmlFormatter
{
    public function formatAs(string $presentation, string $nodeAsString, ?string $url = null): string
    {
        switch ($presentation) {
            case LinkRenderer::PRESENTATION_URL:
                // return the first url
                return $url ?? '';

            case LinkRenderer::PRESENTATION_NORMAL:
            case LinkRenderer::PRESENTATION_CLASS_SHORT:
                $parts = explode('\\', $nodeAsString);
                if (count($parts) <= 1) {
                    $caption = $nodeAsString;
                    break;
                }

                $caption = sprintf('<abbr title="%s">%s</abbr>', $nodeAsString, end($parts));
                break;

            case LinkRenderer::PRESENTATION_FILE_SHORT:
                $parts = explode('/', $nodeAsString);
                if (count($parts) <= 1) {
                    $caption = $nodeAsString;
                    break;
                }

                $caption = sprintf('<abbr title="%s">%s</abbr>', $nodeAsString, end($parts));
                break;

            case LinkRenderer::PRESENTATION_NONE:
                $caption = $nodeAsString;
                break;

            default:
                $caption = sprintf('<abbr title="%s">%s</abbr>', $nodeAsString, $presentation);
                break;
        }

        if (!$url) {
            return $caption;
        }

        return sprintf('<a href="%s">%s</a>', $url, $caption);
    }
}
