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

namespace phpDocumentor\JsonPath;

use phpDocumentor\JsonPath\AST\Comparison;
use phpDocumentor\JsonPath\AST\CurrentNode;
use phpDocumentor\JsonPath\AST\FieldAccess;
use phpDocumentor\JsonPath\AST\FieldName;
use phpDocumentor\JsonPath\AST\FilterNode;
use phpDocumentor\JsonPath\AST\FunctionCall;
use phpDocumentor\JsonPath\AST\Path;
use phpDocumentor\JsonPath\AST\RootNode;
use phpDocumentor\JsonPath\AST\Value;
use phpDocumentor\JsonPath\Fixtures\Book;
use phpDocumentor\JsonPath\Fixtures\Commic;
use phpDocumentor\JsonPath\Fixtures\Store;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ExecutorTest extends TestCase
{
    public function testQueryRootSource(): void
    {
        $this->markTestSkipped('Need to research this, do we need array access');
        $store = new Store();
        $executor = new Executor();
        $result = $executor->evaluate(
            new Path(
                [
                    new RootNode(),
                    new FieldAccess(new FieldName('store')),
                ]
            ),
            ['store' => $store]
        );

        self::assertSame($store, $result);
    }

    public function testQueryRootSourceObject(): void
    {
        $root = new stdClass();
        $store = new Store();
        $root->store = $store;
        $executor = new Executor();
        $result = $executor->evaluate(
            new Path(
                [
                    new RootNode(),
                    new FieldAccess(new FieldName('store')),
                ]
            ),
            $root
        );

        self::assertSame($store, $result);
    }

    public function testQuerySubProperty(): void
    {
        $root = new stdClass();
        $store = new Store();
        $store->addBook(new Book('First book'));
        $store->addBook(new Book('Second book'));
        $root->store = $store;
        $executor = new Executor();
        $result = $executor->evaluate(
            new Path(
                [
                    new RootNode(),
                    new FieldAccess(new FieldName('store')),
                    new FieldAccess(new FieldName('books')),
                ]
            ),
            $root
        );

        self::assertSame($store->getBooks(), $result);
    }

    public function testQuerySubPropertyByFilter(): void
    {
        $book = new Book('phpDoc');
        $root = new stdClass();
        $store = new Store();
        $store->addBook(new Book('First book'));
        $store->addBook($book);
        $store->addBook(new Book('Second book'));
        $root->store = $store;

        $executor = new Executor();
        $result = $executor->evaluate(
            new Path(
                [
                    new RootNode(),
                    new FieldAccess(new FieldName('store')),
                    new FieldAccess(new FieldName('books')),
                    new FilterNode(
                        new Comparison(
                            new Path([
                                new CurrentNode(),
                                new FieldAccess(new FieldName('title')),
                            ]),
                            '==',
                            new Value(
                                'phpDoc'
                            )
                        )
                    ),
                ]
            ),
            $root
        );

        self::assertSame([$book], $result);
    }

    public function testQuerySubPropertyByFilterFunctionCall(): void
    {
        $book = new Commic('phpDoc');
        $root = new stdClass();
        $store = new Store();
        $store->addBook(new Book('First book'));
        $store->addBook($book);
        $store->addBook(new Book('Second book'));
        $root->store = $store;

        $executor = new Executor();
        $result = $executor->evaluate(
            new Path(
                [
                    new RootNode(),
                    new FieldAccess(
                        new FieldName('store')
                    ),
                    new FieldAccess(
                        new FieldName('books')
                    ),
                    new FilterNode(
                        new Comparison(
                            new FunctionCall(
                                'type',
                                new Path([
                                    new CurrentNode(),
                                ])
                            ),
                            '==',
                            new Value(
                                'Commic'
                            )
                        )
                    ),
                ]
            ),
            $root
        );

        self::assertSame([$book], $result);
    }
}
