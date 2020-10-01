<?php
declare(strict_types=1);

namespace phpDocumentor\Guides;

use phpDocumentor\Guides\RestructuredText\Nodes\DocumentNode;

interface Parser
{
    public function parse(string $contents): DocumentNode;

    public function getDocument(): DocumentNode;
}
