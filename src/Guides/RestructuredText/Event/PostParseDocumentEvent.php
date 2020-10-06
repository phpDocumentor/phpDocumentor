<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Event;

use Doctrine\Common\EventArgs;
use phpDocumentor\Guides\Nodes\DocumentNode;

final class PostParseDocumentEvent extends EventArgs
{
    public const POST_PARSE_DOCUMENT = 'postParseDocument';

    /** @var DocumentNode */
    private $documentNode;

    public function __construct(DocumentNode $documentNode)
    {
        $this->documentNode = $documentNode;
    }

    public function getDocumentNode() : DocumentNode
    {
        return $this->documentNode;
    }
}
