<?php

declare(strict_types=1);

namespace phpDocumentor\Guides;

use phpDocumentor\Guides\Nodes\Node;

final class Configuration
{
    /** @return array<class-string<Node>, string>  */
    public function htmlNodeTemplates(): array
    {
        return require __DIR__ . '/../resources/config/html.php';
    }
}
