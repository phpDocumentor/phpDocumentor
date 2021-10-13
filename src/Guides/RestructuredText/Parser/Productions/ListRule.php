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

use phpDocumentor\Guides\Nodes\ListItemNode;
use phpDocumentor\Guides\Nodes\ListNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\ParagraphNode;
use phpDocumentor\Guides\RestructuredText\Parser;
use phpDocumentor\Guides\RestructuredText\Parser\Buffer;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentParser;
use phpDocumentor\Guides\RestructuredText\Parser\LinesIterator;
use Webmozart\Assert\Assert;

use function count;
use function ltrim;
use function max;
use function mb_strlen;
use function preg_match;
use function preg_replace;
use function str_repeat;
use function strlen;
use function strpos;
use function substr;
use function trim;

/**
 * @link https://docutils.sourceforge.io/docs/ref/rst/restructuredtext.html#bullet-lists
 */
final class ListRule implements Rule
{
    /**
     * A regex matching all bullet list markers and a subset of the enumerated list markers.
     *
     * @see https://docutils.sourceforge.io/docs/ref/rst/restructuredtext.html#bullet-lists
     * @see https://docutils.sourceforge.io/docs/ref/rst/restructuredtext.html#enumerated-lists
     */
    public const LIST_MARKER = '/
        ^(
            [-+*\x{2022}\x{2023}\x{2043}]     # match bullet list markers: "*", "+", "-", "•", "‣", or "⁃"
            |(?:[\d#]+\.|[\d#]+\)|\([\d#]+\))
             # match arabic (1-9) or auto-enumerated ("#") lists with formats: "1.", "1)", or "(1)"
        )
        (?:\s+|$)
         # capture the spaces between marker and text to determine the list item text offset
         # (or eol, if text starts on a new line)
        /ux';

    /** @var Parser */
    private $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    public function applies(DocumentParser $documentParser): bool
    {
        $documentIterator = $documentParser->getDocumentIterator();

        // Lists should/cannot occur as the first line in a document; otherwise it is hard to have the following:
        // 1. * is an asterisk
        return $documentIterator->atStart() === false
            && $this->isListLine($documentIterator->current());
    }

    public function apply(LinesIterator $documentIterator, ?Node $on = null): ?Node
    {
        $listOffset = 0;
        $listMarker = null;
        $buffer = new Buffer();
        $this->isListLine($documentIterator->current(), $listMarker, $listOffset);
        $buffer->push($documentIterator->current());

        while (
            $documentIterator->getNextLine() !== null
            && (
                $this->isListLine($documentIterator->getNextLine(), $listMarker, $listOffset)
                || $this->isBlockLine($documentIterator->getNextLine(), max(1, $listOffset))
            )
        ) {
            $documentIterator->next();

            // the list item offset is determined by the offset of the first text.
            // An offset of 1 or lower indicates that the list line didn't contain any text.
            if ($listOffset <= 1) {
                $listOffset = strlen($documentIterator->current()) - strlen(ltrim($documentIterator->current()));
            }

            $buffer->push($documentIterator->current());
        }

        $list = $this->parseList($buffer->getLines());

        return new ListNode($list, $list[0]->isOrdered());
    }

    public function isListLine(
        string $line,
        ?string &$listMarker = null,
        ?int &$listOffset = 0,
        ?string $nextLine = null
    ): bool {
        $isList = preg_match(self::LIST_MARKER, $line, $m) > 0;
        if (!$isList) {
            return false;
        }

        $offset = strlen($m[0]);
        $normalizedMarker = preg_replace('/\d+/', 'd', $m[1]);
        if (
            // validate if next line can be considered part of a list for enumerated lists
            $normalizedMarker !== $m[1]
            && $nextLine !== null
            && trim($nextLine) !== ''
            && !$this->isBlockLine($nextLine, $offset)
            && !$this->isListLine($nextLine, $normalizedMarker)
        ) {
            return false;
        }

        if ($listMarker !== null) {
            $isList = $normalizedMarker === $listMarker;
        }

        if ($isList) {
            $listOffset = $offset;
            $listMarker = $normalizedMarker;
        }

        return $isList;
    }

    /**
     * Is this line "indented"?
     *
     * A blank line also counts as a "block" line, as it
     * may be the empty line between, for example, a
     * ".. note::" directive and the indented content on the
     * next lines.
     *
     * @param int $minIndent can be used to require a specific level of
     *                       indentation for non-blank lines (number of spaces)
     */
    public function isBlockLine(string $line, int $minIndent = 1): bool
    {
        return trim($line) === '' || $this->isIndented($line, $minIndent);
    }

    /**
     * Check if line is an indented one.
     *
     * This does *not* include blank lines, use {@see isBlockLine()} to check
     * for blank or indented lines.
     *
     * @param int $minIndent can be used to require a specific level of indentation (number of spaces)
     */
    public function isIndented(string $line, int $minIndent = 1): bool
    {
        return strpos($line, str_repeat(' ', $minIndent)) === 0;
    }

    /**
     * @param string[] $lines
     *
     * @return ListItemNode[]
     */
    public function parseList(array $lines): array
    {
        $list = [];
        $currentItem = null;
        $currentPrefix = null;
        $currentOffset = 0;

        $createListItem = function (string $item, string $prefix): ListItemNode {
            // parse any markup in the list item (e.g. sublists, directives)
            $nodes = $this->parser->getSubParser()->parse($this->parser->getEnvironment(), $item)->getNodes();
            if (count($nodes) === 1 && $nodes[0] instanceof ParagraphNode) {
                // if there is only one paragraph node, the value is put directly in the <li> element
                $nodes = [$nodes[0]->getValue()];
            }

            Assert::allIsInstanceOf($nodes, Node::class);

            return new ListItemNode($prefix, mb_strlen($prefix) > 1, $nodes);
        };

        foreach ($lines as $line) {
            if (preg_match(self::LIST_MARKER, $line, $m) > 0) {
                // a list marker indicates the start of a new list item,
                // complete the previous one and start a new one
                if ($currentItem !== null) {
                    $list[] = $createListItem($currentItem, $currentPrefix);
                }

                $currentOffset = strlen($m[0]);
                $currentPrefix = $m[1];
                $currentItem = substr($line, $currentOffset) . "\n";

                continue;
            }

            // the list item offset is determined by the offset of the first text
            if (trim($currentItem) === '') {
                $currentOffset = strlen($line) - strlen(ltrim($line));
            }

            $currentItem .= substr($line, $currentOffset) . "\n";
        }

        if ($currentItem !== null) {
            $list[] = $createListItem($currentItem, $currentPrefix);
        }

        return $list;
    }
}
