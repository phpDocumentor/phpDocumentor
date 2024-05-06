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

namespace phpDocumentor\JsonPath\Parser;

use Generator;
use Parsica\Parsica\Parser;
use phpDocumentor\JsonPath\AST\Comparison;
use phpDocumentor\JsonPath\AST\CurrentNode;
use phpDocumentor\JsonPath\AST\FieldAccess;
use phpDocumentor\JsonPath\AST\FieldName;
use phpDocumentor\JsonPath\AST\FilterNode;
use phpDocumentor\JsonPath\AST\FunctionCall;
use phpDocumentor\JsonPath\AST\Path;
use phpDocumentor\JsonPath\AST\RootNode;
use phpDocumentor\JsonPath\AST\Value;
use phpDocumentor\JsonPath\AST\Wildcard;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ParserBuilderTest extends TestCase
{
    private Parser $parser;

    protected function setUp(): void
    {
        $this->parser = (new ParserBuilder())->build();
    }

    public function testRootNodeIsParsed(): void
    {
        $result = $this->parser->tryString('$');
        self::assertEquals(new RootNode(), $result->output());
    }

    public function testCurrentNodeIsParsed(): void
    {
        $result = $this->parser->tryString('@');
        self::assertEquals(new CurrentNode(), $result->output());
    }

    public function testCurrentNodeChildren(): void
    {
        $result = $this->parser->tryString('@.*');
        self::assertEquals(new Path([new CurrentNode(), new FieldAccess(new Wildcard())]), $result->output());
    }

    public function testRootFieldAccess(): void
    {
        $result = $this->parser->tryString('$.store');

        self::assertEquals(
            new Path([
                new RootNode(),
                new FieldAccess(
                    new FieldName('store'),
                ),
            ]),
            $result->output(),
        );
    }

    public function testRootFieldAccessArrayLike(): void
    {
        $result = $this->parser->tryString('$[\'store\']');

        self::assertEquals(
            new Path([
                new RootNode(),
                new FieldAccess(
                    new FieldName('store'),
                ),
            ]),
            $result->output(),
        );
    }

    public function testRootFieldChildAccess(): void
    {
        $result = $this->parser->tryString('$.store.address');

        self::assertEquals(
            new Path([
                new RootNode(),
                new FieldAccess(
                    new FieldName('store'),
                ),
                new FieldAccess(
                    new FieldName('address'),
                ),
            ]),
            $result->output(),
        );
    }

    #[DataProvider('operatorProvider')]
    public function testFilterExpression(string $operator): void
    {
        $result = $this->parser->tryString('$.store.books[?(@.title ' . $operator . ' "phpDoc")]');
        self::assertEquals(
            new Path([
                new RootNode(),
                new FieldAccess(
                    new FieldName('store'),
                ),
                new FieldAccess(
                    new FieldName('books'),
                ),
                new FilterNode(
                    new Comparison(
                        new Path([
                            new CurrentNode(),
                            new FieldAccess(new FieldName('title')),
                        ]),
                        $operator,
                        new Value(
                            'phpDoc',
                        ),
                    ),
                ),
            ]),
            $result->output(),
        );
    }

    /** @return Generator<string, string> */
    public static function operatorProvider(): Generator
    {
        $operators = [
            '==',
            '!=',
            'starts_with',
        ];

        foreach ($operators as $operator) {
            yield $operator => [$operator];
        }
    }

    public function testFilterExpressionCurrentObjectProperyWildCard(): void
    {
        $result = $this->parser->tryString('$.store.books[?(@.* == "phpDoc")]');
        self::assertEquals(
            new Path([
                new RootNode(),
                new FieldAccess(
                    new FieldName('store'),
                ),
                new FieldAccess(
                    new FieldName('books'),
                ),
                new FilterNode(
                    new Comparison(
                        new Path([
                            new CurrentNode(),
                            new FieldAccess(new Wildcard()),
                        ]),
                        '==',
                        new Value(
                            'phpDoc',
                        ),
                    ),
                ),
            ]),
            $result->output(),
        );
    }

    public function testFilterExpressionCurrentObjectTypeEquals(): void
    {
        $result = $this->parser->tryString('$.store.books[?(type(@) == "api")]');
        self::assertEquals(
            new Path([
                new RootNode(),
                new FieldAccess(
                    new FieldName('store'),
                ),
                new FieldAccess(
                    new FieldName('books'),
                ),
                new FilterNode(
                    new Comparison(
                        new FunctionCall(
                            'type',
                            new CurrentNode(),
                        ),
                        '==',
                        new Value(
                            'api',
                        ),
                    ),
                ),
            ]),
            $result->output(),
        );
    }

    public function testFilterExpressionCurrentObjectChildrenTypeEquals(): void
    {
        $result = $this->parser->tryString('$.store.books[?(type(@.*) == "api")]');
        self::assertEquals(
            new Path([
                new RootNode(),
                new FieldAccess(
                    new FieldName('store'),
                ),
                new FieldAccess(
                    new FieldName('books'),
                ),
                new FilterNode(
                    new Comparison(
                        new FunctionCall(
                            'type',
                            new Path([
                                new CurrentNode(),
                                new FieldAccess(new Wildcard()),
                            ]),
                        ),
                        '==',
                        new Value(
                            'api',
                        ),
                    ),
                ),
            ]),
            $result->output(),
        );
    }

    public function testFilterExpressionWildcard(): void
    {
        $result = $this->parser->tryString('$.store.books_title[*]');
        self::assertEquals(
            new Path([
                new RootNode(),
                new FieldAccess(
                    new FieldName('store'),
                ),
                new FieldAccess(
                    new FieldName('books_title'),
                ),
                new FilterNode(
                    new Wildcard(),
                ),
            ]),
            $result->output(),
        );
    }

    public function testFilterExpressionWildcardNested(): void
    {
        $result = $this->parser->tryString('$.store.books_title.*[*]');
        self::assertEquals(
            new Path([
                new RootNode(),
                new FieldAccess(
                    new FieldName('store'),
                ),
                new FieldAccess(
                    new FieldName('books_title'),
                ),
                new FieldAccess(
                    new Wildcard(),
                ),
                new FilterNode(
                    new Wildcard(),
                ),
            ]),
            $result->output(),
        );
    }
}
