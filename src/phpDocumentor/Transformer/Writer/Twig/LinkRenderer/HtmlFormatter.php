<?php

declare(strict_types=1);

namespace phpDocumentor\Transformer\Writer\Twig\LinkRenderer;

use phpDocumentor\Transformer\Writer\Twig\LinkRenderer;

use function sprintf;

class HtmlFormatter
{
    public function format(Target $target): string
    {
        // plain text presentation of the url of the target, or if that is null for some reason: the title.
        if ($target->getPresentation() === LinkRenderer::PRESENTATION_URL) {
            return $target->getUrl() ?? $target->getTitle();
        }

        return $this->decorateWithAnchor(
            $this->decorateWithAbbreviation($target->getTitle(), $target),
            $target,
        );
    }

    private function decorateWithAbbreviation(string $caption, Target $target): string
    {
        if ($target->getAbbreviation() === null) {
            return $caption;
        }

        return sprintf('<abbr title="%s">%s</abbr>', $caption, $target->getAbbreviation());
    }

    private function decorateWithAnchor(string $caption, Target $target): string
    {
        if ($target->getUrl() === null) {
            return $caption;
        }

        return sprintf('<a href="%s">%s</a>', $target->getUrl(), $caption);
    }
}
