<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser\Productions;

use InvalidArgumentException;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\SectionEndNode;
use phpDocumentor\Guides\Nodes\TitleNode;
use phpDocumentor\Guides\RestructuredText\Directives\Directive as DirectiveHandler;
use phpDocumentor\Guides\RestructuredText\MarkupLanguageParser;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentParser;
use phpDocumentor\Guides\RestructuredText\Parser\LineDataParser;
use phpDocumentor\Guides\RestructuredText\Parser\LinesIterator;

use function array_search;

final class DocumentRule implements Rule
{
    /** @var DocumentParser */
    private $documentParser;

    /** @var Rule[] */
    private $productions;

    /**
     * @param DirectiveHandler[] $directiveHandlers
     */
    public function __construct(
        DocumentParser $documentParser,
        MarkupLanguageParser $parser,
        array $directiveHandlers
    ) {
        $this->documentParser = $documentParser;

        $lineDataParser = new LineDataParser($parser);

        $literalBlockRule = new LiteralBlockRule();

        // TODO: Somehow move this into the top of the instantiation chain so that you can configure which rules
        //       to use when consuming this library
        $this->productions = [
            new TitleRule($parser, $documentParser),
            new TransitionRule(), // Transition rule must follow Title rule
            new LinkRule($lineDataParser, $parser),
            $literalBlockRule,
            new BlockQuoteRule($parser),
            new ListRule($parser),
            new DirectiveRule($parser, $documentParser, $lineDataParser, $literalBlockRule, $directiveHandlers),
            new CommentRule(),
            new DefinitionListRule($lineDataParser),
            new TableRule($parser),

            // For now: ParagraphRule must be last as it is the rule that applies if none other applies.
            new ParagraphRule($parser, $documentParser),
        ];
    }

    public function applies(DocumentParser $documentParser): bool
    {
        return $documentParser->getDocumentIterator()->atStart();
    }

    public function apply(LinesIterator $documentIterator, ?Node $on = null): ?Node
    {
        if ($on instanceof DocumentNode === false) {
            throw new InvalidArgumentException('Expected a document to apply this compound rule on');
        }

        $this->documentParser->lastTitleNode = null;
        $this->documentParser->openSectionsAsTitleNodes->exchangeArray([]); // clear it

        // We explicitly do not use foreach, but rather the cursors of the DocumentIterator
        // this is done because we are transitioning to a method where a Substate can take the current
        // cursor as starting point and loop through the cursor
        while ($documentIterator->valid()) {
            foreach ($this->productions as $production) {
                if (!$production->applies($this->documentParser)) {
                    continue;
                }

                $newNode = $production->apply($documentIterator, $on);
                if ($newNode !== null) {
                    $on->addNode($newNode);
                }

                break;
            }

            $documentIterator->next();
        }

        // TODO: Can we get rid of this here? It would make this parser cleaner and if it is part of the
        //       Title/SectionRule itself it is neatly encapsulated.
        foreach ($this->documentParser->openSectionsAsTitleNodes as $titleNode) {
            $this->endOpenSection($on, $titleNode);
        }

        return $on;
    }

    public function endOpenSection(DocumentNode $document, TitleNode $titleNode): void
    {
        $document->addNode(new SectionEndNode($titleNode));

        $key = array_search($titleNode, $this->documentParser->openSectionsAsTitleNodes->getArrayCopy(), true);

        if ($key === false) {
            return;
        }

        unset($this->documentParser->openSectionsAsTitleNodes[$key]);
    }
}
