<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Span;

use phpDocumentor\Faker\Faker;
use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\ReferenceBuilder;
use phpDocumentor\Guides\Span\SpanToken;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use function current;

final class SpanParserTest extends TestCase
{
    use Faker;
    use ProphecyTrait;

    /** @var Environment&ObjectProphecy */
    private $environment;

    /** @var ReferenceBuilder&ObjectProphecy */
    private $referenceRegistry;

    /** @var SpanParser */
    private $spanProcessor;

    public function setUp(): void
    {
        $this->referenceRegistry = $this->prophesize(ReferenceBuilder::class);
        $this->environment = $this->prophesize(Environment::class);
        $this->environment->getTitleLetters()->willReturn([]);
        $this->environment->resetAnonymousStack()->hasReturnVoid();
        $this->spanProcessor = new SpanParser($this->referenceRegistry->reveal());
    }

    public function testInlineLiteralsAreReplacedWithToken(): void
    {
        $result = $this->spanProcessor->process(
            $this->environment->reveal(),
            'This text is an example of ``inline literals``.'
        );
        $token = current($this->spanProcessor->getTokens());

        self::assertStringNotContainsString('``inline literals``', $result);
        self::assertInstanceOf(SpanToken::class, $token);
        self::assertEquals(SpanToken::TYPE_LITERAL, $token->getType());
        self::assertEquals(
            [
                'type' => 'literal',
                'text' => 'inline literals',
            ],
            $token->getTokenData()
        );
    }

    /** @dataProvider invalidNotationsProvider */
    public function testIncompleteStructuresAreIgnored(string $input): void
    {
        $result = $this->spanProcessor->process($this->environment->reveal(), $input);

        self::assertSame($input, $result);
        self::assertCount(0, $this->spanProcessor->getTokens());
    }

    public function invalidNotationsProvider(): array
    {
        return [
            'Literal start without end' => ['This text is an example of `` mis-used.'],
            'Backtick without end' => ['This text is an example of `  ` mis-used.'],
            'Embedded url start outside context' => ['This text is an example of <a>'],
        ];
    }

    /** @dataProvider namedHyperlinkReferenceProvider */
    public function testNamedHyperlinkReferencesAreReplaced(
        string $input,
        string $referenceId,
        string $text,
        string $url = ''
    ): void {
        $result = $this->spanProcessor->process($this->environment->reveal(), $input);
        $token = current($this->spanProcessor->getTokens());

        self::assertInstanceOf(SpanToken::class, $token);
        self::assertEquals(SpanToken::TYPE_LINK, $token->getType());
        self::assertEquals(
            [
                'type' => SpanToken::TYPE_LINK,
                'url' => $url,
                'link' => $text,
            ],
            $token->getTokenData()
        );
        self::assertRegExp($referenceId, $result);

        if ($url === '') {
            return;
        }

        $this->environment->setLink($text, $url)->shouldHaveBeenCalledOnce();
    }

    /** string[][[] */
    public function namedHyperlinkReferenceProvider(): array
    {
        return [
            [
                'This text is an example of link_.',
                '#This text is an example of [a-z0-9]{40}\\.#',
                'link',
            ],
            [
                'This text is an example of `Phrase Reference`_.',
                '#This text is an example of [a-z0-9]{40}\\.#',
                'Phrase Reference',
            ],
            [
                'This text is an example of `Phrase < Reference`_',
                '#This text is an example of [a-z0-9]{40}#',
                'Phrase < Reference',
            ],
            [
                <<<TEXT
This text is an example of `Phrase
                 Reference`_.
TEXT
,
                '#This text is an example of [a-z0-9]{40}#',
                'Phrase Reference',
            ],
            [
                'This is an example of `embedded urls <http://google.com>`_ in a text',
                '#This is an example of [a-z0-9]{40} in a text#',
                'embedded urls',
                'http://google.com',
            ],
            [
                'This is an example of `embedded urls alias <alias_>`_ in a text',
                '#This is an example of [a-z0-9]{40} in a text#',
                'embedded urls alias',
                'alias_',
            ],
            [
                'A more complex example `\__call() <https://www.php.net/language.oop5.overloading#object.call>`_.',
                '#A more complex example [a-z0-9]{40}\\.#',
                '__call()',
                'https://www.php.net/language.oop5.overloading#object.call',
            ],
        ];
    }

    /** @dataProvider AnonymousHyperlinksProvider */
    public function testAnonymousHyperlinksAreReplacedWithToken(
        string $input,
        string $referenceId,
        string $text,
        string $url = ''
    ): void {
        $this->testNamedHyperlinkReferencesAreReplaced($input, $referenceId, $text, $url);
        $this->environment->pushAnonymous($text)->shouldHaveBeenCalled()->hasReturnVoid();
    }

    public function AnonymousHyperlinksProvider(): array
    {
        return [
            [
                'This is an example of an link__',
                '#This is an example of an [a-z0-9]{40}#',
                'link',
            ],
        ];
    }

    public function testInlineInternalTargetsAreReplaced(): void
    {
        $result = $this->spanProcessor->process($this->environment->reveal(), 'Some _`internal ref` in text.');
        $token = current($this->spanProcessor->getTokens());

        self::assertStringNotContainsString('_`internal ref`', $result);
        self::assertInstanceOf(SpanToken::class, $token);
        self::assertEquals(SpanToken::TYPE_LINK, $token->getType());
        self::assertEquals(
            [
                'type' => SpanToken::TYPE_LINK,
                'url' => '',
                'link' => 'internal ref',
            ],
            $token->getTokenData()
        );
    }

    public function testFootNoteReferencesAreReplaced(): void
    {
        $this->markTestSkipped('Footnotes are not supported yet');
        $result = $this->spanProcessor->process($this->environment->reveal(), 'Please RTFM [1]_.');
        $token = current($this->spanProcessor->getTokens());

        self::assertStringNotContainsString('[1]_', $result);
        self::assertInstanceOf(SpanToken::class, $token);
        self::assertEquals(SpanToken::TYPE_REFERENCE, $token->getType());
        self::assertEquals(
            [
                'type' => SpanToken::TYPE_REFERENCE,
                'url' => '_`internal ref`',
                'link' => 'internal ref',
            ],
            $token->getTokenData()
        );
    }

    public function testEmailAddressesAreReplacedWithToken(): void
    {
        $email = $this->faker()->email;

        $result = $this->spanProcessor->process($this->environment->reveal(), $email);
        $tokens = $this->spanProcessor->getTokens();
        $token = current($tokens);

        self::assertStringNotContainsString($email, $result);
        self::assertCount(1, $tokens);
        self::assertSame(SpanToken::TYPE_LINK, $token->getType());
        self::assertSame(
            [
                'link' => $email,
                'url' => 'mailto:' . $email,
                'type' => SpanToken::TYPE_LINK,
            ],
            $token->getTokenData()
        );
    }

    public function testInlineUrlsAreReplacedWithToken(): void
    {
        $url = $this->faker()->url;

        $result = $this->spanProcessor->process($this->environment->reveal(), $url);
        $tokens = $this->spanProcessor->getTokens();
        $token = current($tokens);

        self::assertStringNotContainsString($url, $result);
        self::assertCount(1, $tokens);
        self::assertSame(SpanToken::TYPE_LINK, $token->getType());
        self::assertSame(
            [
                'link' => $url,
                'url' => $url,
                'type' => SpanToken::TYPE_LINK,
            ],
            $token->getTokenData()
        );
    }

    public function testNoReplacementsAreDoneWhenNotNeeded(): void
    {
        $result = $this->spanProcessor->process($this->environment->reveal(), 'Raw token');
        self::assertSame('Raw token', $result);
        self::assertEmpty($this->spanProcessor->getTokens());
    }
}
