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

namespace phpDocumentor\Descriptor;

use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Tests the functionality for the Collection class.
 *
 * @coversDefaultClass \phpDocumentor\Descriptor\Collection
 */
final class CollectionTest extends TestCase
{
    /** @var Collection $fixture */
    private $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp(): void
    {
        $this->fixture = new Collection();
    }

    /**
     * @covers ::__construct
     */
    public function testInitialize(): void
    {
        $fixture = new Collection();

        self::assertEmpty($fixture->getAll());
    }

    /**
     * @covers ::__construct
     */
    public function testInitializeWithExistingArray(): void
    {
        $expected = [1, 2];
        $fixture = new Collection($expected);

        self::assertEquals($expected, $fixture->getAll());
    }

    /**
     * @covers ::add
     */
    public function testAddNewItem(): void
    {
        $expected = ['abc'];
        $expectedSecondRun = ['abc', 'def'];

        $this->fixture->add('abc');

        self::assertEquals($expected, $this->fixture->getAll());

        $this->fixture->add('def');

        self::assertEquals($expectedSecondRun, $this->fixture->getAll());
    }

    /**
     * @covers ::set
     * @covers ::offsetSet
     */
    public function testSetItemsWithKey(): void
    {
        $expected = ['z' => 'abc'];
        $expectedSecondRun = ['z' => 'abc', 'y' => 'def'];

        self::assertEquals([], $this->fixture->getAll());

        $this->fixture->set('z', 'abc');

        self::assertEquals($expected, $this->fixture->getAll());

        $this->fixture->set('y', 'def');

        self::assertEquals($expectedSecondRun, $this->fixture->getAll());
    }

    /**
     * @covers ::set
     */
    public function testSetItemsWithEmptyKeyShouldThrowException(): void
    {
        $this->expectException('InvalidArgumentException');
        $this->fixture->set('', 'abc');
    }

    /**
     * @covers ::offsetSet
     */
    public function testSetItemsUsingOffsetSetWithEmptyKeyShouldThrowException(): void
    {
        $this->expectException('InvalidArgumentException');
        $this->fixture->offsetSet('', 'abc');
    }

    /**
     * @covers ::get
     * @covers ::__get
     * @covers ::offsetGet
     */
    public function testRetrievalOfItems(): void
    {
        $this->fixture['a'] = 'abc';
        self::assertEquals('abc', $this->fixture->__get('a'));
        self::assertEquals('abc', $this->fixture['a']);
        self::assertEquals('abc', $this->fixture->get('a'));
        self::assertCount(1, $this->fixture);

        self::assertEquals('def', $this->fixture->fetch(1, 'def'));
        self::assertCount(2, $this->fixture);
    }

    /**
     * @covers ::getAll
     */
    public function testRetrieveAllItems(): void
    {
        $this->fixture['a'] = 'abc';
        self::assertSame(['a' => 'abc'], $this->fixture->getAll());
    }

    /**
     * @covers ::first
     */
    public function testRetrieveFirstItem(): void
    {
        $this->fixture['a'] = 'abc';
        $this->fixture['b'] = 'def';

        self::assertSame('abc', $this->fixture->first());
    }

    /**
     * @covers ::getIterator
     */
    public function testGetIterator(): void
    {
        $this->fixture['a'] = 'abc';
        self::assertInstanceOf('ArrayIterator', $this->fixture->getIterator());
        self::assertSame(['a' => 'abc'], $this->fixture->getIterator()->getArrayCopy());
    }

    /**
     * @covers ::count
     * @covers ::offsetUnset
     */
    public function testCountReturnsTheNumberOfElements(): void
    {
        self::assertCount(0, $this->fixture);
        self::assertEquals(0, $this->fixture->count());

        $this->fixture[0] = 'abc';

        self::assertCount(1, $this->fixture);
        self::assertEquals(1, $this->fixture->count());

        $this->fixture[1] = 'def';

        self::assertCount(2, $this->fixture);
        self::assertEquals(2, $this->fixture->count());

        unset($this->fixture[0]);

        self::assertCount(1, $this->fixture);
        self::assertEquals(1, $this->fixture->count());
    }

    /**
     * @covers ::clear
     */
    public function testClearingTheCollection(): void
    {
        $this->fixture[1] = 'a';
        $this->fixture[2] = 'b';

        self::assertCount(2, $this->fixture);

        $this->fixture->clear();

        self::assertCount(0, $this->fixture);
    }

    /**
     * @covers ::offsetExists
     */
    public function testIfExistingElementsAreDetected(): void
    {
        self::assertArrayNotHasKey(0, $this->fixture);
        self::assertFalse($this->fixture->offsetExists(0));

        $this->fixture[0] = 'abc';

        self::assertArrayHasKey(0, $this->fixture);
        self::assertTrue($this->fixture->offsetExists(0));
    }

    /**
     * @covers ::merge
     */
    public function testIfAfterMergeCollectionContainsAllItems(): void
    {
        $expected = [0 => 'a', 1 => 'b', 2 => 'c'];
        $this->fixture[1] = 'a';
        $this->fixture[2] = 'b';

        $collection2 = new Collection();
        $collection2[4] = 'c';

        $result = $this->fixture->merge($collection2);

        self::assertSame($expected, $result->getAll());
    }

    /**
     * @covers ::filter
     */
    public function testFilterReturnsOnlyInstancesOfCertainType(): void
    {
        $expected = [0 => new stdClass()];

        $this->fixture[0] = new stdClass();
        $this->fixture[1] = false;
        $this->fixture[2] = 'string';

        $result = $this->fixture->filter(stdClass::class)->getAll();

        self::assertEquals($expected, $result);
    }
}
