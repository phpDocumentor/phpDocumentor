<?php

declare(strict_types=1);

namespace phpDocumentor\Guides;

class Configuration
{
    public function htmlNodeTemplates(): array
    {
        return require __DIR__  . '/../resources/config/html.php';
    }
}
