<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\TextRoles;

use phpDocumentor\Guides\Nodes\InlineToken\PHPReferenceNode;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentParserContext;
use phpDocumentor\Reflection\Fqsen;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

final class PHPReferenceTest extends TestCase
{
    private PHPReference $textRole;

    protected function setUp(): void
    {
        $this->textRole = new PHPReference(new NullLogger());
    }

    /** @dataProvider phpRefrenceProvider */
    public function testProcessNodeReturnsNode(string $role, string $content, PHPReferenceNode $expected): void
    {
        $node = $this->textRole->processNode(
            $this->createMock(DocumentParserContext::class),
            $role,
            $content,
            $content,
        );

        $this->assertEquals(
            $expected,
            $node,
        );
    }

    public function phpRefrenceProvider(): iterable
    {
        yield [
            'role' => 'php:class',
            'content' => '\phpDocumentor\Reflection\Fqsen',
            'expected' => new PHPReferenceNode(
                'class',
                new Fqsen('\phpDocumentor\Reflection\Fqsen'),
            ),
        ];

        yield [
            'role' => 'php:class',
            'content' => 'Fqen<\phpDocumentor\Reflection\Fqsen>',
            'expected' => new PHPReferenceNode(
                'class',
                new Fqsen('\phpDocumentor\Reflection\Fqsen'),
                'Fqen',
            ),
        ];

        yield [
            'role' => 'php:method',
            'content' => '\phpDocumentor\Reflection\Fqsen::test()',
            'expected' => new PHPReferenceNode(
                'method',
                new Fqsen('\phpDocumentor\Reflection\Fqsen::test()'),
            ),
        ];

        yield [
            'role' => 'php:class',
            'content' => 'phpDocumentor\Reflection\Fqsen',
            'expected' => new PHPReferenceNode(
                'class',
                new Fqsen('\phpDocumentor\Reflection\Fqsen'),
            ),
        ];
    }
}
