<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser\Productions;

use phpDocumentor\Guides\Nodes\CodeNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\Parser\Buffer;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentIterator;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentParser;

final class CodeProduction implements Production
{
    public function applies(DocumentParser $documentParser): bool
    {
        $isBlockLine = $this->isBlockLine($documentParser->getDocumentIterator()->current());

        return ($isBlockLine && $documentParser->isCode);
    }


    public function trigger(DocumentIterator $documentIterator): ?Node
    {
        $buffer = new Buffer();
        $buffer->push($documentIterator->current());

        while ($documentIterator->getNextLine() !== null && $this->isBlockLine($documentIterator->getNextLine())) {
            $documentIterator->next();
            $buffer->push($documentIterator->current());
        }

        return new CodeNode($buffer->getLines());
    }

    private function isBlockLine(string $line): bool
    {
        if ($line !== '') {
            return trim($line[0]) === '';
        }

        return trim($line) === '';
    }
}
