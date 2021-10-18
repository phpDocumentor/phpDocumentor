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

use InvalidArgumentException;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\SectionBeginNode;
use phpDocumentor\Guides\Nodes\SectionEndNode;
use phpDocumentor\Guides\Nodes\SpanNode;
use phpDocumentor\Guides\Nodes\TitleNode;
use phpDocumentor\Guides\RestructuredText\MarkupLanguageParser;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentParser;
use phpDocumentor\Guides\RestructuredText\Parser\LinesIterator;

use function array_search;
use function in_array;
use function strlen;
use function trim;

/**
 * @link https://docutils.sourceforge.io/docs/ref/rst/restructuredtext.html#sections
 *
 * @todo convert the TitleRule into a separate SectionRule that can nest itself and close itself when a lower-level
 *       title is encountered
 */
final class TitleRule implements Rule
{
    private const HEADER_LETTERS = [
        '!',
        '"',
        '#',
        '$',
        '%',
        '&',
        '\'',
        '(',
        ')',
        '*',
        '+',
        ',',
        '-',
        '.',
        '/',
        ':',
        ';',
        '<',
        '=',
        '>',
        '?',
        '@',
        '[',
        '\\',
        ']',
        '^',
        '_',
        '`',
        '{',
        '|',
        '}',
        '~',
    ];

    /** @var MarkupLanguageParser */
    private $parser;

    /** @var DocumentParser */
    private $documentParser;

    public function __construct(MarkupLanguageParser $parser, DocumentParser $documentParser)
    {
        $this->parser = $parser;
        $this->documentParser = $documentParser;
    }

    public function applies(DocumentParser $documentParser): bool
    {
        $line = $documentParser->getDocumentIterator()->current();
        $nextLine = $documentParser->getDocumentIterator()->getNextLine();

        return $this->currentLineIsAnOverline($line, $nextLine)
            || $this->nextLineIsAnUnderline($line, $nextLine);
    }

    public function apply(LinesIterator $documentIterator, ?Node $on = null): ?Node
    {
        if ($on instanceof DocumentNode === false) {
            throw new InvalidArgumentException('Titles may only be children of documents');
        }

        $title = '';
        $overlineLetter = $this->currentLineIsAnOverline(
            $documentIterator->current(),
            $documentIterator->getNextLine()
        );

        if ($overlineLetter !== '') {
            $documentIterator->next();
            $title = trim($documentIterator->current()); // Title with over and underlines may be indented
        }

        $underlineLetter = $this->nextLineIsAnUnderline($documentIterator->current(), $documentIterator->getNextLine());
        if ($underlineLetter !== '') {
            if (($overlineLetter === '' || $overlineLetter === $underlineLetter)) {
                $title = trim($documentIterator->current()); // Title with over and underlines may be indented
                $documentIterator->next();
            } else {
                $underlineLetter = '';
            }
        }

        $environment = $this->parser->getEnvironment();

        $letter = $overlineLetter ?: $underlineLetter;
        $level = $environment->getLevel($letter);
        $level = $environment->getInitialHeaderLevel() + $level - 1;

        $node = new TitleNode(new SpanNode($this->parser, $title), $level);

        $this->transitionBetweenSections($node, $on);

        return $node;
    }

    public function isSpecialLine(string $line): ?string
    {
        if (strlen($line) < 2) {
            return null;
        }

        $letter = $line[0];

        if (!in_array($letter, self::HEADER_LETTERS, true)) {
            return null;
        }

        for ($i = 1; $i < strlen($line); $i++) {
            if ($line[$i] !== $letter) {
                return null;
            }
        }

        return $letter;
    }

    private function currentLineIsAnOverline(string $line, ?string $nextLine): string
    {
        $letter = $this->isSpecialLine($line);
        if ($nextLine !== null && $letter && $this->isTextLine($nextLine)) {
            return $letter;
        }

        return '';
    }

    private function nextLineIsAnUnderline(string $line, ?string $nextLine): string
    {
        $letter = $nextLine !== null ? $this->isSpecialLine($nextLine) : '';

        if ($letter && $this->isTextLine($line)) {
            return $letter;
        }

        return '';
    }

    private function isTextLine(string $line): bool
    {
        return trim($line) !== '';
    }

    private function transitionBetweenSections(TitleNode $node, DocumentNode $on): void
    {
        // TODO: Is this a Title parser, or actually a Section parser? :thinking_face:
        if ($this->documentParser->lastTitleNode !== null) {
            // current level is less than previous so we need to end all open sections
            if ($node->getLevel() < $this->documentParser->lastTitleNode->getLevel()) {
                foreach ($this->documentParser->openSectionsAsTitleNodes as $titleNode) {
                    $this->endOpenSection($titleNode, $on);
                }

                // same level as the last so just close the last open section
            } elseif ($node->getLevel() === $this->documentParser->lastTitleNode->getLevel()) {
                $this->endOpenSection($this->documentParser->lastTitleNode, $on);
            }
        }

        $this->beginOpenSection($node, $on);
    }

    private function beginOpenSection(TitleNode $node, DocumentNode $on): void
    {
        $this->documentParser->lastTitleNode = $node;
        $on->addNode(new SectionBeginNode($node));
        $this->documentParser->openSectionsAsTitleNodes->append($node);
    }

    private function endOpenSection(TitleNode $titleNode, DocumentNode $on): void
    {
        $on->addNode(new SectionEndNode($titleNode));

        $key = array_search($titleNode, $this->documentParser->openSectionsAsTitleNodes->getArrayCopy(), true);

        if ($key === false) {
            return;
        }

        unset($this->documentParser->openSectionsAsTitleNodes[$key]);
    }
}
