<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser\Productions;

use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\Parser\Buffer;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentIterator;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentParser;

final class CommentProduction implements Production
{
    public function applies(DocumentParser $documentParser): bool
    {
        return $this->isCommentLine($documentParser->getDocumentIterator()->current());
    }

    public function trigger(DocumentIterator $documentIterator): ?Node
    {
        $buffer = new Buffer();
        $buffer->push($documentIterator->current());

        while ($documentIterator->getNextLine() !== null && $this->isCommentLine($documentIterator->getNextLine())) {
            $documentIterator->next();
            $buffer->push($documentIterator->current());
        }

        // TODO: Would we want to keep a comment as a Node in the AST?
        return null;
    }

    private function isCommentLine(string $line): bool
    {
        return $this->isComment($line) || (trim($line) !== '' && $line[0] === ' ');
    }

    private function isComment(string $line): bool
    {
        return preg_match('/^\.\. (.*)$/mUsi', $line) > 0;
    }
}
