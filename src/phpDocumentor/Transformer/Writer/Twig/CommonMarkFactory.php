<?php

declare(strict_types=1);

namespace phpDocumentor\Transformer\Writer\Twig;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\ExtensionInterface;

final class CommonMarkFactory
{
    /** @param iterable<ExtensionInterface> $extensions */
    public function createConverter(iterable $extensions) : CommonMarkConverter
    {
        $environment = Environment::createCommonMarkEnvironment();
        foreach ($extensions as $extension) {
            $environment->addExtension($extension);
        }

        return new CommonMarkConverter([], $environment);
    }
}
