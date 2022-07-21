<?php

declare(strict_types=1);

namespace unit\Parser\Productions;

use League\Flysystem\FilesystemInterface;
use phpDocumentor\Guides\Nodes\ParagraphNode;
use phpDocumentor\Guides\Nodes\SpanNode;
use phpDocumentor\Guides\ParserContext;
use phpDocumentor\Guides\RestructuredText\MarkupLanguageParser;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentParser;
use phpDocumentor\Guides\RestructuredText\Parser\LinesIterator;
use phpDocumentor\Guides\RestructuredText\Parser\Productions\ParagraphRule;
use phpDocumentor\Guides\RestructuredText\Span\SpanParser;
use phpDocumentor\Guides\UrlGenerator;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

use function implode;

final class ParagraphRuleTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @uses \phpDocumentor\Guides\RestructuredText\Parser\LinesIterator
     *
     * @covers \phpDocumentor\Guides\RestructuredText\Parser\Productions\ParagraphRule::__construct
     * @covers \phpDocumentor\Guides\RestructuredText\Parser\Productions\ParagraphRule::apply
     * @covers \phpDocumentor\Guides\RestructuredText\Parser\Productions\ParagraphRule::applies
     * @covers \phpDocumentor\Guides\RestructuredText\Parser\Productions\ParagraphRule::isWhiteline
     * @dataProvider paragraphProvider
     */
    public function testParagraphNodeFromLinesIterator(
        string $input,
        ParagraphNode $node,
        ?string $nextLine,
        bool $nextLiteral = false
    ): void {
        $iterator = new LinesIterator();
        $iterator->load($input);

        $parser = $this->prophesize(MarkupLanguageParser::class);
        $parser->getEnvironment()->willReturn(
            new ParserContext(
                'test',
                'test',
                1,
                $this->prophesize(FilesystemInterface::class)->reveal(),
                new UrlGenerator()
            )
        );
        $documentParser = $this->prophesize(DocumentParser::class);
        $documentParser->getDocumentIterator()->willReturn($iterator);
        $spanParser = $this->prophesize(SpanParser::class);
        $spanParser->parse(
            Argument::any(),
            Argument::any()
        )->will(function ($args) {
            return new SpanNode(implode("\n", $args[0]));
        });

        $rule = new ParagraphRule(
            $parser->reveal(),
            $documentParser->reveal(),
            $spanParser->reveal()
        );

        self::assertTrue($rule->applies($documentParser->reveal()));
        $result = $rule->apply($iterator);
        self::assertEquals(
            $node,
            $result
        );

        self::assertSame($nextLine, $iterator->getNextLine());
        self::assertSame($nextLiteral, $documentParser->nextIndentedBlockShouldBeALiteralBlock);
    }

    public function paragraphProvider(): array
    {
        return [
            [
                'input' => 'some text.',
                'output' => new ParagraphNode(new SpanNode('some text.', [])),
                'remaining' => null,
            ],
            [
                'input' => <<<RST
some multiline
paragraph
RST
,
                'output' => new ParagraphNode(
                    new SpanNode(
                        <<<RST
some multiline
paragraph
RST,
                        []
                    )
                ),
                'remaining' => null,
            ],
            [
                'input' => <<<RST
some multiline
paragraph

This is a new paragraph
RST
                ,
                'output' => new ParagraphNode(
                    new SpanNode(
                        <<<RST
some multiline
paragraph
RST,
                        []
                    )
                ),
                'remaining' => '',
            ],
            [
                'input' => <<<RST
some multiline
paragraph

This is a new paragraph
RST
                ,
                'output' => new ParagraphNode(
                    new SpanNode(
                        <<<RST
some multiline
paragraph
RST,
                        []
                    )
                ),
                'remaining' => '',
            ],
            [
                'input' => <<<RST
some multiline next paragraph is a literal block
paragraph::

  This is a new paragraph
RST
                ,
                'output' => new ParagraphNode(
                    new SpanNode(
                        <<<RST
some multiline next paragraph is a literal block
paragraph:
RST,
                        []
                    )
                ),
                'remaining' => '',
                'nextLiteral' => true,
            ],
            [
                'input' => <<<RST
some multiline next paragraph is a literal block
paragraph::

  This is a new paragraph
RST
                ,
                'output' => new ParagraphNode(
                    new SpanNode(
                        <<<RST
some multiline next paragraph is a literal block
paragraph:
RST,
                        []
                    )
                ),
                'remaining' => '',
                'nextLiteral' => true,
            ],
            [
                'input' => <<<RST
some multiline next paragraph is a literal block
paragraph: ::

  This is a new paragraph
RST
                ,
                'output' => new ParagraphNode(
                    new SpanNode(
                        <<<RST
some multiline next paragraph is a literal block
paragraph:
RST,
                        []
                    )
                ),
                'remaining' => '',
                'nextLiteral' => true,
            ],
            [
                'input' => <<<RST
some multiline next paragraph is a literal block
paragraph:

::

  This is a new paragraph
RST
            ,
                'output' => new ParagraphNode(
                    new SpanNode(
                        <<<RST
some multiline next paragraph is a literal block
paragraph:
RST,
                        []
                    )
                ),
                'remaining' => '',
                'nextLiteral' => false,
            ],
        ];
    }
}
