<?php

declare(strict_types=1);

namespace phpDocumentor\Transformer\Writer\Twig;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Extension\ExtensionInterface;

final class CommonMarkFactory
{
    /** @param iterable<ExtensionInterface> $extensions */
    public function createConverter(iterable $extensions): CommonMarkConverter
    {
        $converter = new CommonMarkConverter([]);
        foreach ($extensions as $extension) {
            $converter->getEnvironment()->addExtension($extension);
        }

        return $converter;
    }
}
