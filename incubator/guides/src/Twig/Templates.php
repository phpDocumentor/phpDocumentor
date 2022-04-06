<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Twig;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

class Templates
{
    public static function create(): Filesystem
    {
        return new Filesystem(new Local(__DIR__ . '/../../resources/template'));
    }
}
