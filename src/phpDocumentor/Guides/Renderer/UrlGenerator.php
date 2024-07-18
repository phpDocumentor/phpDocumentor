<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Renderer;

use phpDocumentor\Guides\RenderContext;
use phpDocumentor\Guides\Renderer\UrlGenerator\AbstractUrlGenerator;

use function str_starts_with;
use function trim;

final class UrlGenerator extends AbstractUrlGenerator
{
    public function generateInternalPathFromRelativeUrl(RenderContext $renderContext, string $canonicalUrl): string
    {
        if ($renderContext->getDestinationPath() === '') {
            return $canonicalUrl;
        }

        $prefix = trim($renderContext->getDestinationPath(), '/') . '/';

        if (str_starts_with($canonicalUrl, $prefix)) {
            return $canonicalUrl;
        }

        return $prefix . $canonicalUrl;
    }
}
