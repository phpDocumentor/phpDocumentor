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
use phpDocumentor\JsonPath\AST\LogicalAnd;
use phpDocumentor\JsonPath\AST\LogicalOr;
use phpDocumentor\JsonPath\AST\Path;
use phpDocumentor\JsonPath\AST\RootNode;
use phpDocumentor\JsonPath\AST\Value;
use phpDocumentor\JsonPath\AST\Wildcard;
use phpDocumentor\JsonPath\Fixtures\ArrayAccessibleStore;
use phpDocumentor\JsonPath\Fixtures\Book;
use phpDocumentor\JsonPath\Fixtures\Commic;
use phpDocumentor\JsonPath\Fixtures\Store;
use PHPUnit\Framework\TestCase;
use stdClass;

use function iterator_to_array;

final class ExecutorTest extends TestCase
{
    public function testQueryRootSource(): void
    {
        $store = new Store();
        $executor = new Executor();
        $result = $executor->evaluate(
            new Path(
                [
                    new RootNode(),
                    new FieldAccess(new FieldName('store')),
                ],
            ),
            ['store' => $store],
        );

        self::assertSame([$store], iterator_to_array($result, false));
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
                ],
            ),
            $root,
        );

        self::assertSame([$store], iterator_to_array($result));
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
                    new FieldAccess(new Wildcard()),
                ],
            ),
            $root,
        );

        self::assertSame($store->getBooks(), iterator_to_array($result, false));
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
                    new FieldAccess(new Wildcard()),
                    new FilterNode(
                        new Comparison(
                            new Path([
                                new CurrentNode(),
                                new FieldAccess(new FieldName('title')),
                            ]),
                            '==',
                            new Value(
                                'phpDoc',
                            ),
                        ),
                    ),
                ],
            ),
            $root,
        );

        self::assertSame([$book], iterator_to_array($result, false));
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
                        new FieldName('store'),
                    ),
                    new FieldAccess(
                        new FieldName('books'),
                    ),
                    new FieldAccess(
                        new Wildcard(),
                    ),
                    new FilterNode(
                        new Comparison(
                            new FunctionCall(
                                'type',
                                new Path([
                                    new CurrentNode(),
                                ]),
                            ),
                            '==',
                            new Value(
                                'Commic',
                            ),
                        ),
                    ),
                ],
            ),
            $root,
        );

        self::assertSame([$book], iterator_to_array($result, false));
    }

    public function testQueryWithWildcard(): void
    {
        $books = [
            'phpDoc',
            'First book',
            'Second book',
        ];

        $root = new stdClass();
        $root->store = $this->createStore($books);

        $executor = new Executor();
        $result = $executor->evaluate(
            new Path(
                [
                    new RootNode(),
                    new FieldAccess(
                        new FieldName('store'),
                    ),
                    new FieldAccess(
                        new FieldName('books'),
                    ),
                    new FilterNode(
                        new Wildcard(),
                    ),
                    new FieldAccess(
                        new FieldName('title'),
                    ),
                ],
            ),
            $root,
        );

        self::assertSame($books, iterator_to_array($result, false));
    }

    public function testQueryCollectionInCollection(): void
    {
        $books = [
            'phpDoc',
            'First book',
            'Second book',
        ];

        $root = new stdClass();
        $root->stores = [];

        $root->stores[] = $this->createStore($books);
        $root->stores[] = $this->createStore(['foo', 'bar']);
        $root->stores[] = $this->createStore($books);

        $executor = new Executor();
        $result = $executor->evaluate(
            new Path(
                [
                    new RootNode(),
                    new FieldAccess(
                        new FieldName('stores'),
                    ),
                    new FilterNode(
                        new Wildcard(),
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
                            '==',
                            new Value(
                                'phpDoc',
                            ),
                        ),
                    ),
                    new FieldAccess(
                        new FieldName('title'),
                    ),
                ],
            ),
            $root,
        );

        self::assertEquals(['phpDoc', 'phpDoc'], iterator_to_array($result, false));
    }

    public function testQueryWithLogicalAnd(): void
    {
        $books = [
            'For',
            'First book',
            'Fifth book',
        ];

        $root = new stdClass();
        $root->store = $this->createStore($books);

        $executor = new Executor();
        $result = $executor->evaluate(
            new Path(
                [
                    new RootNode(),
                    new FieldAccess(
                        new FieldName('store'),
                    ),
                    new FieldAccess(
                        new FieldName('books'),
                    ),
                    new FieldAccess(
                        new Wildcard(),
                    ),
                    new FilterNode(
                        new LogicalAnd(
                            new Comparison(
                                new Path([
                                    new CurrentNode(),
                                    new FieldAccess(new FieldName('title')),
                                ]),
                                'starts_with',
                                new Value(
                                    'F',
                                ),
                            ),
                            new Comparison(
                                new Path([
                                    new CurrentNode(),
                                    new FieldAccess(new FieldName('title')),
                                ]),
                                'starts_with',
                                new Value(
                                    'Fi',
                                ),
                            ),
                        ),
                    ),
                    new FieldAccess(
                        new FieldName('title'),
                    ),
                ],
            ),
            $root,
        );

        self::assertSame(['First book', 'Fifth book'], iterator_to_array($result, false));
    }

    public function testQueryWithLogicalOr(): void
    {
        $books = [
            'For',
            'First book',
            'Second book',
        ];

        $root = new stdClass();
        $root->store = $this->createStore($books);

        $executor = new Executor();
        $result = $executor->evaluate(
            new Path(
                [
                    new RootNode(),
                    new FieldAccess(
                        new FieldName('store'),
                    ),
                    new FieldAccess(
                        new FieldName('books'),
                    ),
                    new FieldAccess(
                        new Wildcard(),
                    ),
                    new FilterNode(
                        new LogicalOr(
                            new Comparison(
                                new Path([
                                    new CurrentNode(),
                                    new FieldAccess(new FieldName('title')),
                                ]),
                                'starts_with',
                                new Value(
                                    'For',
                                ),
                            ),
                            new Comparison(
                                new Path([
                                    new CurrentNode(),
                                    new FieldAccess(new FieldName('title')),
                                ]),
                                'starts_with',
                                new Value(
                                    'Second',
                                ),
                            ),
                        ),
                    ),
                    new FieldAccess(
                        new FieldName('title'),
                    ),
                ],
            ),
            $root,
        );

        self::assertSame(['For', 'Second book'], iterator_to_array($result, false));
    }

    public function testEvaluateNotEqualsComparison(): void
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
                    new FieldAccess(new Wildcard()),
                    new FilterNode(
                        new Comparison(
                            new Path([
                                new CurrentNode(),
                                new FieldAccess(new FieldName('title')),
                            ]),
                            '!=',
                            new Value('phpDoc'),
                        ),
                    ),
                    new FieldAccess(new FieldName('title')),
                ],
            ),
            $root,
        );

        self::assertSame(['First book', 'Second book'], iterator_to_array($result, false));
    }

    public function testEvaluateContainsComparisonMatch(): void
    {
        $executor = new Executor();
        $result = $executor->evaluateContainsComparison(
            null,
            ['tags' => ['php', 'json', 'path']],
            new Path([new CurrentNode(), new FieldAccess(new FieldName('tags'))]),
            new Value('json'),
        );

        self::assertTrue($result);
    }

    public function testEvaluateContainsComparisonNoMatch(): void
    {
        $executor = new Executor();
        $result = $executor->evaluateContainsComparison(
            null,
            ['tags' => ['php', 'json', 'path']],
            new Path([new CurrentNode(), new FieldAccess(new FieldName('tags'))]),
            new Value('xml'),
        );

        self::assertFalse($result);
    }

    public function testEvaluateContainsComparisonOnNonIterableValueIsFalse(): void
    {
        $executor = new Executor();
        $result = $executor->evaluateContainsComparison(
            null,
            ['title' => 'phpDoc'],
            new Path([new CurrentNode(), new FieldAccess(new FieldName('title'))]),
            new Value('php'),
        );

        self::assertFalse($result);
    }

    public function testEvaluateEqualsComparisonWithNonStringValue(): void
    {
        $executor = new Executor();
        $result = $executor->evaluateEqualsComparison(
            null,
            ['count' => 5],
            new Path([new CurrentNode(), new FieldAccess(new FieldName('count'))]),
            new Value(5),
        );

        self::assertTrue($result);
    }

    public function testFieldAccessOnArrayAccessObject(): void
    {
        $root = new stdClass();
        $root->store = new ArrayAccessibleStore(['title' => 'phpDoc']);

        $executor = new Executor();
        $result = $executor->evaluate(
            new Path([
                new RootNode(),
                new FieldAccess(new FieldName('store')),
                new FieldAccess(new FieldName('title')),
            ]),
            $root,
        );

        self::assertSame(['phpDoc'], iterator_to_array($result, false));
    }

    public function testFieldAccessOnMissingArrayKeyYieldsNothing(): void
    {
        $executor = new Executor();
        $result = $executor->evaluate(
            new Path([
                new RootNode(),
                new FieldAccess(new FieldName('missing')),
            ]),
            ['store' => 'x'],
        );

        self::assertSame([], iterator_to_array($result, false));
    }

    public function testFieldAccessOnMissingObjectPropertyYieldsNothing(): void
    {
        $executor = new Executor();
        $result = $executor->evaluate(
            new Path([
                new RootNode(),
                new FieldAccess(new FieldName('missingProperty')),
            ]),
            new Store(),
        );

        self::assertSame([], iterator_to_array($result, false));
    }

    public function testFieldAccessOnNonObjectScalarYieldsNothing(): void
    {
        $executor = new Executor();
        $result = $executor->evaluate(
            new Path([
                new RootNode(),
                new FieldAccess(new FieldName('title')),
            ]),
            'just a string',
        );

        self::assertSame([], iterator_to_array($result, false));
    }

    public function testWildcardFieldAccessOnNonIterableYieldsNothing(): void
    {
        $executor = new Executor();
        $result = $executor->evaluate(
            new Path([
                new RootNode(),
                new FieldAccess(new Wildcard()),
            ]),
            'just a string',
        );

        self::assertSame([], iterator_to_array($result, false));
    }

    public function testFunctionCallWithUnknownFunctionNameReturnsNull(): void
    {
        $executor = new Executor();
        $result = $executor->evaluate(
            new FunctionCall('unknownFunction', new CurrentNode()),
            'irrelevant',
        );

        self::assertNull($result);
    }

    public function testEvaluateRootNodeReturnsRootElement(): void
    {
        $executor = new Executor();
        $result = $executor->evaluate(new RootNode(), 'current', 'root');

        self::assertSame('root', $result);
    }

    public function testEvaluateCurrentNodeReturnsCurrentElement(): void
    {
        $executor = new Executor();
        $result = $executor->evaluate(new CurrentNode(), 'current', 'root');

        self::assertSame('current', $result);
    }

    public function testQueryThreeLevelsOfNestedCollections(): void
    {
        $root = new stdClass();
        $root->warehouses = [];

        $warehouse = new stdClass();
        $warehouse->stores = [
            $this->createStore(['phpDoc', 'phpDoc']),
            $this->createStore(['other', 'other']),
        ];
        $root->warehouses[] = $warehouse;

        $otherWarehouse = new stdClass();
        $otherWarehouse->stores = [$this->createStore(['phpDoc'])];
        $root->warehouses[] = $otherWarehouse;

        $executor = new Executor();
        $result = $executor->evaluate(
            new Path(
                [
                    new RootNode(),
                    new FieldAccess(new FieldName('warehouses')),
                    new FilterNode(new Wildcard()),
                    new FieldAccess(new FieldName('stores')),
                    new FilterNode(new Wildcard()),
                    new FieldAccess(new FieldName('books')),
                    new FilterNode(
                        new Comparison(
                            new Path([
                                new CurrentNode(),
                                new FieldAccess(new FieldName('title')),
                            ]),
                            '==',
                            new Value('phpDoc'),
                        ),
                    ),
                    new FieldAccess(new FieldName('title')),
                ],
            ),
            $root,
        );

        self::assertSame(['phpDoc', 'phpDoc', 'phpDoc'], iterator_to_array($result, false));
    }

    /**
     * When a store contains a mix of matching and non-matching book titles, a filter applied three
     * collection-levels deep must still filter per book and must not leak non-matching siblings through.
     *
     * This used to be broken: the field-access flattening treated a Generator wrapping multiple parent
     * elements (produced by an intermediate wildcard filter) differently from a plain array of parent
     * elements, so books ended up batched per store instead of flattened individually before filtering.
     * The comparison's toValue() then only inspected the *first* book's title in each batch and, if it
     * matched, yielded the *entire* batch unfiltered. See Executor::evaluateFieldAccess().
     */
    public function testQueryThreeLevelsOfNestedCollectionsDoesNotLeakNonMatchingSiblingsWhenTitlesAreMixed(): void
    {
        $root = new stdClass();
        $root->warehouses = [];

        $warehouse = new stdClass();
        $warehouse->stores = [
            $this->createStore(['phpDoc', 'other']),
            $this->createStore(['phpDoc']),
        ];
        $root->warehouses[] = $warehouse;

        $otherWarehouse = new stdClass();
        $otherWarehouse->stores = [$this->createStore(['unrelated'])];
        $root->warehouses[] = $otherWarehouse;

        $executor = new Executor();
        $result = $executor->evaluate(
            new Path(
                [
                    new RootNode(),
                    new FieldAccess(new FieldName('warehouses')),
                    new FilterNode(new Wildcard()),
                    new FieldAccess(new FieldName('stores')),
                    new FilterNode(new Wildcard()),
                    new FieldAccess(new FieldName('books')),
                    new FilterNode(
                        new Comparison(
                            new Path([
                                new CurrentNode(),
                                new FieldAccess(new FieldName('title')),
                            ]),
                            '==',
                            new Value('phpDoc'),
                        ),
                    ),
                    new FieldAccess(new FieldName('title')),
                ],
            ),
            $root,
        );

        self::assertSame(['phpDoc', 'phpDoc'], iterator_to_array($result, false));
    }

    private function createStore(array $books): Store
    {
        $store = new Store();
        foreach ($books as $title) {
            $store->addBook(new Book($title));
        }

        return $store;
    }
}
