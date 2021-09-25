<?php


namespace phpDocumentor\Guides\RestructuredText\Parser\Productions;


use phpDocumentor\Guides\Nodes\CodeNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\Parser\Buffer;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentIterator;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentParser;

interface Production
{

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
    public function trigger(DocumentIterator $documentIterator): ?Node;

    public function applies(DocumentParser $documentParser): bool;
}
