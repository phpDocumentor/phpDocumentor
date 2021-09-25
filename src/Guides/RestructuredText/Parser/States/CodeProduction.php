<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser\States;

use phpDocumentor\Guides\Nodes\CodeNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\Parser\Buffer;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentIterator;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentParser;

final class CodeProduction
{
    public function applies(DocumentParser $documentParser)
    {
        $isBlockLine = $this->isBlockLine($documentParser->getDocumentIterator()->current());

        return ($isBlockLine && $documentParser->isCode);
    }

    /**
     * Enters this state and loops through all relevant lines until a Node is produced.
     *
     * The opening line is considered relevant and as such is always used (this is found the case in the
     * {@see self::Applies()} method, otherwise we wouldn't have been here) but for all subsequent lines we use a Look
     * Ahead to test whether it should be included in the Node.
     *
     * By using a Look Ahead, we prevent the cursor from advancing; and this caused the cursor to 'rest' on the line
     * that is considered that last relevant line. The document parser will advance the line after successfully parsing
     * this and to send the Parser into a line that belongs to another state.
     *
     * @param DocumentIterator $documentIterator
     * @return Node|null
     */
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

    public function isBlockLine(string $line): bool
    {
        if ($line !== '') {
            return trim($line[0]) === '';
        }

        return trim($line) === '';
    }
}
