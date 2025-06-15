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

use ArrayAccess;
use ArrayIterator;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use OutOfRangeException;
use ReturnTypeWillChange;
use Webmozart\Assert\Assert;

use function array_filter;
use function array_key_first;
use function array_merge;
use function count;

/**
 * Represents an easily accessible collection of elements.
 *
 * The goal for this class is to allow Descriptors to be easily retrieved and set so that interaction in
 * templates becomes easier.
 *
 * @template T
 * @template-implements ArrayAccess<array-key, T>
 * @template-implements IteratorAggregate<array-key, T>
 */
class Collection implements Countable, IteratorAggregate, ArrayAccess
{
    /**
     * Constructs a new collection object with optionally a series of items, generally Descriptors.
     *
     * @param array<T> $items
     */
    public function __construct(protected array $items = [])
    {
    }

    /**
     * Adds a new item to this collection, generally a Descriptor.
     *
     * @param T $item
     */
    public function add($item): void
    {
        $this->items[] = $item;
    }

    /**
     * Sets a new object onto the collection or clear it using null.
     *
     * @param array-key $index An index value to recognize this item with.
     * @param T          $item  The item to store, generally a Descriptor but may be something else.
     */
    public function set($index, $item): void
    {
        $this->offsetSet($index, $item);
    }

    /**
     * Retrieves a specific item from the Collection with its index. If index is not found, an exception is thrown
     *
     * @param array-key $index
     *
     * @return T The contents of the element with the given index
     */
    public function get($index)
    {
        if (! isset($this->items[$index])) {
            throw new OutOfRangeException($index . ' offset not found in Collection');
        }

        return $this->items[$index];
    }

    /**
     * Retrieves a specific item from the Collection with its index.
     *
     * Please note that this method (intentionally) has the side effect that whenever a key does not exist that it will
     * be created with the value provided by the $valueIfEmpty argument. This will allow for easy initialization during
     * tree building operations.
     *
     * @param array-key $index
     * @param ?TChild      $valueIfEmpty If the index does not exist it will be created with this value and returned.
     *
     * @return TChild The contents of the element with the given index and the provided default if the key
     *                doesn't exist.
     * @psalm-return ($valueIfEmpty is null ? ?TChild : TChild)
     * @phpstan-return T|TChild
     *
     * @template TChild as T
     */
    public function fetch($index, $valueIfEmpty = null)
    {
        if (! $this->offsetExists($index) && $valueIfEmpty !== null) {
            /** @var T $valueIfEmpty */
            $this->offsetSet($index, $valueIfEmpty);
        }

        return $this->offsetGet($index);
    }

    /** @return ?T */
    public function first()
    {
        if (count($this->items) === 0) {
            return null;
        }

        return $this->items[array_key_first($this->items)];
    }

    /**
     * Retrieves all items from this collection as PHP Array.
     *
     * @return array<T>
     */
    public function getAll(): array
    {
        return $this->items;
    }

    /**
     * Retrieves an iterator to traverse this object.
     *
     * @return ArrayIterator<array-key, T>
     */
    #[ReturnTypeWillChange]
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    /**
     * Returns a count of the number of elements in this collection.
     */
    #[ReturnTypeWillChange]
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Empties the collection.
     */
    public function clear(): void
    {
        $this->items = [];
    }

    /**
     * Retrieves an item as if it were a property of the collection.
     *
     * @return mixed
     * @phpstan-return ?T
     */
    public function __get(string $name)
    {
        return $this->get($name);
    }

    /**
     * Checks whether an item in this collection exists.
     *
     * @param array-key $offset The index to check on.
     */
    #[ReturnTypeWillChange]
    public function offsetExists($offset): bool
    {
        return isset($this->items[$offset]);
    }

    /**
     * Retrieves an item from the collection with the given index.
     *
     * @param array-key $offset The offset to retrieve.
     *
     * @return ?T
     */
    #[ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->items[$offset] : null;
    }

    /**
     * Sets an item at the given index.
     *
     * @param array-key|null $offset The offset to assign the value to.
     * @param T           $value  The value to set.
     *
     * @throws InvalidArgumentException If the key is null or an empty string.
     */
    #[ReturnTypeWillChange]
    public function offsetSet($offset, $value): void
    {
        if ($offset === '' || $offset === null) {
            throw new InvalidArgumentException('The key of a collection must always be set');
        }

        Assert::notNull($value);

        $this->items[$offset] = $value;
    }

    /**
     * Removes an item with the given index from the collection.
     *
     * @param array-key $offset The offset to unset.
     */
    #[ReturnTypeWillChange]
    public function offsetUnset($offset): void
    {
        unset($this->items[$offset]);
    }

    /**
     * Returns a new collection with the items from this collection and the provided combined.
     *
     * @param Collection<T> $collection
     *
     * @return Collection<T>
     */
    public function merge(self $collection): Collection
    {
        return new self(array_merge($collection->getAll(), $this->items));
    }

    /**
     * @param class-string<F> $className
     *
     * @return Collection<F>
     *
     * @template F of object
     */
    public function filter(string $className): Collection
    {
        /** @var Collection<F> $collection */
        $collection = new self(
            array_filter(
                $this->getAll(),
                static fn ($item) => $item instanceof $className,
            ),
        );

        return $collection;
    }

    /**
     * @param callable(T):bool $callback
     *
     * @return self<T>
     */
    public function matches(callable $callback): Collection
    {
        return new self(
            array_filter(
                $this->getAll(),
                $callback,
            ),
        );
    }

    /**
     * @param class-string<C> $classString
     * @param array<C> $elements
     *
     * @return Collection<C>
     *
     * @template C
     */
    public static function fromClassString(string $classString, array $elements = []): Collection
    {
        Assert::classExists($classString);

        return new Collection($elements);
    }

    /**
     * @param class-string<C> $interfaceString
     * @param array<C> $elements
     *
     * @return Collection<C>
     *
     * @template C
     */
    public static function fromInterfaceString(string $interfaceString, array $elements = []): Collection
    {
        Assert::interfaceExists($interfaceString);

        return new Collection($elements);
    }
}
