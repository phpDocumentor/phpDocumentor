<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Guides\RestructuredText\Parser\Productions;

use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentParser;
use phpDocumentor\Guides\RestructuredText\Parser\LinesIterator;

interface Rule
{
    public function applies(DocumentParser $documentParser): bool;

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
     */
    public function apply(LinesIterator $documentIterator, ?Node $on = null): ?Node;
}
