<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser\Subparsers;

use phpDocumentor\Guides\Nodes\Node;

interface Subparser
{
    /**
     * Reset the (sub)parser to be ready to build a new Node.
     */
    public function reset(string $openingLine): void;

    /**
     * Parses the given line and uses it to construct the node.
     *
     * This method returns a boolean indicating whether the given line was accepted to be a part of the Node that we are
     * building. If the line is not accepted, then we assume it belongs to another (type of) Node and the main parser
     * should try to parse it again from the BEGIN state.
     */
    public function parse(string $line): bool;

    /**
     * Combines the parsed/provides lines into an actual Node.
     *
     * After all lines have been passed, the build method is used to combine that information and build a Node that can
     * be added onto the Document. If this method returns null, no Node needed be built (for example: a Comment) and the
     * parser will just continue parsing.
     *
     * @return Node|null
     */
    public function build(): ?Node;
}
