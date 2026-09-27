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

use Generator;
use phpDocumentor\JsonPath\Fixtures\Annotation;
use phpDocumentor\JsonPath\Fixtures\Book;
use phpDocumentor\JsonPath\Fixtures\PopupStore;
use phpDocumentor\JsonPath\Fixtures\Store;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

use function is_array;
use function iterator_to_array;

/**
 * These tests run a JSONPath query string through the *real* parser (`Parser::createInstance()`)
 * and execute the resulting AST with the real `Executor`.
 *
 * `ParserBuilderTest` only asserts the AST shape a query parses into, and `ExecutorTest` only
 * asserts what the `Executor` does with hand-built AST. Neither of those catches a parser that
 * produces an AST the executor doesn't handle the way the query author intended (for example,
 * omitting a `Wildcard` `FieldAccess` that the executor's collection-filtering behaviour relies
 * on). These tests close that gap by exercising the parser and executor together.
 */
final class QueryIntegrationTest extends TestCase
{
    private Executor $executor;

    protected function setUp(): void
    {
        $this->executor = new Executor();
    }

    /** @param mixed $expected */
    #[DataProvider('queryProvider')]
    public function testQueryAgainstRealParserAndExecutor(string $jsonPath, $expected): void
    {
        $root = $this->createRoot();

        $query = Parser::createInstance()->parse($jsonPath);
        $result = $this->executor->evaluate($query, $root, $root);

        if ($result instanceof Generator) {
            $result = iterator_to_array($result, false);
        }

        self::assertEquals($expected, self::titlesOf($result));
    }

    /**
     * Normalises a result so that `Book` instances (and nested collections of them) are
     * represented by their titles, which keeps the data provider readable.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    private static function titlesOf($value)
    {
        if ($value instanceof Book) {
            return $value->getTitle();
        }

        if (is_array($value)) {
            return array_map([self::class, 'titlesOf'], $value);
        }

        return $value;
    }

    public function testRootQueryReturnsTheRootElement(): void
    {
        $root = $this->createRoot();

        $query = Parser::createInstance()->parse('$');
        $result = $this->executor->evaluate($query, $root, $root);

        self::assertSame($root, $result);
    }

    /** @return Generator<string, array{string, mixed}> */
    public static function queryProvider(): Generator
    {
        yield 'sub property' => ['$.store.address', ['My Address']];

        yield 'wildcard over books' => [
            '$.store.books[*].title',
            ['First book', 'phpDoc', 'Second book'],
        ];

        yield 'filter by comparison matches a single book' => [
            '$.store.books[?(@.title == "phpDoc")]',
            ['phpDoc'],
        ];

        yield 'nested filter expression (existence check on chapters)' => [
            '$.store.books[?(@.chapters[?(@.title == "Getting started")])]',
            ['phpDoc'],
        ];
    }

    /**
     * A `&&` that isn't scoped to a single element of a nested collection is a trap: it looks
     * like it requires "one chapter with this title AND one chapter with that annotation", but
     * it actually evaluates each side against the whole (flattened) `chapters` collection
     * independently. `@.chapters.annotations.name` collapses every chapter's annotations into
     * one list and `Comparison`'s value-coercion silently keeps only the *first* entry - so the
     * match depends on chapter order, not on whether any single chapter really satisfies both
     * conditions.
     *
     * Scoping both conditions inside the same nested `chapters` filter (one `[?(...)]` testing
     * `@.title` and `@.annotations[?(...)]` on the very same `@`) is the only way to correctly
     * express "there exists one chapter with title X that also has annotation Y".
     */
    public function testUnscopedLogicalAndAcrossNestedCollectionIsOrderDependentButScopedFilterIsCorrect(): void
    {
        $unscopedQuery = '$.books[*][?(@.chapters[?(@.title == "Getting started")] && '
            . '@.chapters.annotations.name == "important")]';
        $scopedQuery = '$.books[*][?(@.chapters[?(@.title == "Getting started" && '
            . '@.annotations[?(@.name == "important")])])]';

        // Neither chapter has both title "Getting started" and an "important" annotation.
        $gettingStarted = new Book('Getting started');
        $gettingStarted->addAnnotation(new Annotation('draft'));

        $theEnd = new Book('The end');
        $theEnd->addAnnotation(new Annotation('important'));

        $book = new Book('Guide');
        $book->addChapter($gettingStarted);
        $book->addChapter($theEnd);

        self::assertSame(
            [],
            self::queryBookTitles($scopedQuery, $book),
            'no single chapter satisfies both conditions, so the correctly scoped query must not match',
        );

        $chaptersFirstOrder = self::queryBookTitles($unscopedQuery, $book);

        $reorderedBook = new Book('Guide');
        $reorderedBook->addChapter($theEnd);
        $reorderedBook->addChapter($gettingStarted);

        $chaptersReorderedResult = self::queryBookTitles($unscopedQuery, $reorderedBook);

        self::assertNotSame(
            $chaptersFirstOrder,
            $chaptersReorderedResult,
            'the unscoped query incorrectly depends on chapter order instead of consistently finding no match',
        );
    }

    public function testScopedLogicalAndAcrossNestedCollectionMatchesWhenOneChapterSatisfiesBothConditions(): void
    {
        $scopedQuery = '$.books[*][?(@.chapters[?(@.title == "Getting started" && '
            . '@.annotations[?(@.name == "important")])])]';

        $gettingStarted = new Book('Getting started');
        $gettingStarted->addAnnotation(new Annotation('important'));

        $book = new Book('Guide');
        $book->addChapter($gettingStarted);
        $book->addChapter(new Book('The end'));

        self::assertSame(['Guide'], self::queryBookTitles($scopedQuery, $book));
    }

    /**
     * `[*]` (a filter with a wildcard expression, "select every element") must correctly narrow
     * a collection down to matching elements no matter how many parent/sibling elements are
     * present at each nesting level. This is the counterpart to `.*` (field access with a
     * wildcard field name), which only behaves correctly when there happens to be exactly one
     * bundled collection to unwrap and silently breaks with more realistic, multi-element data
     * (see the real-world query this was reported against: `documentationSets.*[?(...)].indexes
     * .classes.*[?(...)]`, which should have used `[*]` instead of `.*` at both levels).
     *
     * This test exercises `[*]` three levels deep - across stores, books and chapters - with
     * multiple, mostly-non-matching siblings at every level, to guard against that class of bug.
     */
    public function testWildcardFilterCorrectlyNarrowsAcrossMultipleNestedCollectionLevels(): void
    {
        $query = '$.stores[*][?(type(@) == \'Store\')].books[*][?(@.chapters[?(@.title == "Getting started" && '
            . '@.annotations[?(@.name == "important")])])]';

        // The one chapter, in the one book, in the one store, that genuinely satisfies both
        // conditions on the same chapter.
        $matchingChapter = new Book('Getting started');
        $matchingChapter->addAnnotation(new Annotation('important'));

        $matchingBook = new Book('The Matching Guide');
        $matchingBook->addChapter($matchingChapter);
        $matchingBook->addChapter(new Book('The end'));

        // A near-miss: right chapter title, wrong annotation - must not match.
        $almostChapter = new Book('Getting started');
        $almostChapter->addAnnotation(new Annotation('draft'));

        $nonMatchingBook = new Book('Another Guide');
        $nonMatchingBook->addChapter($almostChapter);

        $matchingTypeStoreWithMatch = new Store();
        $matchingTypeStoreWithMatch->addBook($nonMatchingBook);
        $matchingTypeStoreWithMatch->addBook($matchingBook);

        $matchingTypeStoreWithoutMatch = new Store();
        $matchingTypeStoreWithoutMatch->addBook(new Book('Unrelated'));

        // Same, genuinely matching, chapter/book content, but in a store of the *wrong* type -
        // must be excluded by the `type(@) == 'Store'` filter regardless.
        $wrongTypeChapter = new Book('Getting started');
        $wrongTypeChapter->addAnnotation(new Annotation('important'));

        $wrongTypeBook = new Book('Should be excluded due to store type');
        $wrongTypeBook->addChapter($wrongTypeChapter);

        $wrongTypeStore = new PopupStore();
        $wrongTypeStore->addBook($wrongTypeBook);

        $root = new stdClass();
        $root->stores = [$wrongTypeStore, $matchingTypeStoreWithMatch, $matchingTypeStoreWithoutMatch];

        $parsedQuery = Parser::createInstance()->parse($query);
        $result = $this->executor->evaluate($parsedQuery, $root, $root);

        if ($result instanceof Generator) {
            $result = iterator_to_array($result, false);
        }

        self::assertSame(['The Matching Guide'], self::titlesOf($result));
    }

    /** @return list<string> */
    private static function queryBookTitles(string $jsonPath, Book $book): array
    {
        $root = new stdClass();
        $root->books = [$book];

        $query = Parser::createInstance()->parse($jsonPath);
        $result = (new Executor())->evaluate($query, $root, $root);

        if ($result instanceof Generator) {
            $result = iterator_to_array($result, false);
        }

        /** @var list<string> */
        return self::titlesOf($result);
    }

    private function createRoot(): stdClass
    {
        $root = new stdClass();
        $store = new Store();

        $store->addBook(new Book('First book'));

        $bookWithChapters = new Book('phpDoc');
        $bookWithChapters->addChapter(new Book('Introduction'));
        $bookWithChapters->addChapter(new Book('Getting started'));
        $store->addBook($bookWithChapters);

        $store->addBook(new Book('Second book'));

        $root->store = $store;

        return $root;
    }
}
