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

namespace phpDocumentor\Descriptor\Interfaces;

use ArrayAccess;
use ArrayIterator;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use ReturnTypeWillChange;

/**
 * Represents an easily accessible collection of elements.
 *
 * The goal for this class is to allow Descriptors to be easily retrieved and set so that interaction in
 * templates becomes easier.
 *
 * @template T
 * @template-extends ArrayAccess<array-key, T>
 * @template-extends IteratorAggregate<array-key, T>
 */
interface Collection extends Countable, IteratorAggregate, ArrayAccess
{
    /**
     * Adds a new item to this collection, generally a Descriptor.
     *
     * @param T $item
     */
    public function add($item): void;

    /**
     * Sets a new object onto the collection or clear it using null.
     *
     * @param array-key $index An index value to recognize this item with.
     * @param T $item The item to store, generally a Descriptor but may be something else.
     */
    public function set($index, $item): void;

    /**
     * Retrieves a specific item from the Collection with its index. If index is not found, an exception is thrown
     *
     * @param array-key $index
     *
     * @return T The contents of the element with the given index
     */
    public function get($index);

    /**
     * Retrieves a specific item from the Collection with its index.
     *
     * Please note that this method (intentionally) has the side effect that whenever a key does not exist that it will
     * be created with the value provided by the $valueIfEmpty argument. This will allow for easy initialization during
     * tree building operations.
     *
     * @param array-key $index
     * @param ?TChild $valueIfEmpty If the index does not exist it will be created with this value and returned.
     *
     * @return TChild The contents of the element with the given index and the provided default if the key
     *                doesn't exist.
     * @psalm-return ($valueIfEmpty is null ? ?TChild : TChild)
     * @phpstan-return T|TChild
     *
     * @template TChild as T
     */
    public function fetch($index, $valueIfEmpty = null);

    /** @return ?T */
    public function first();

    /**
     * Retrieves all items from this collection as PHP Array.
     *
     * @return array<T>
     */
    public function getAll(): array;

    /**
     * Retrieves an iterator to traverse this object.
     *
     * @return ArrayIterator<array-key, T>
     */
    public function getIterator(): ArrayIterator;

    /**
     * Empties the collection.
     */
    public function clear(): void;

    /**
     * Retrieves an item as if it were a property of the collection.
     *
     * @return mixed
     * @phpstan-return ?T
     */
    public function __get(string $name);

    /**
     * Checks whether an item in this collection exists.
     *
     * @param array-key $offset The index to check on.
     */
    public function offsetExists($offset): bool;

    /**
     * Retrieves an item from the collection with the given index.
     *
     * @param array-key $offset The offset to retrieve.
     *
     * @return ?T
     */
    #[ReturnTypeWillChange]
    public function offsetGet($offset);

    /**
     * Sets an item at the given index.
     *
     * @param array-key|null $offset The offset to assign the value to.
     * @param T $value The value to set.
     *
     * @throws InvalidArgumentException If the key is null or an empty string.
     */
    public function offsetSet($offset, $value): void;

    /**
     * Removes an item with the given index from the collection.
     *
     * @param array-key $offset The offset to unset.
     */
    public function offsetUnset($offset): void;

    /**
     * Returns a new collection with the items from this collection and the provided combined.
     *
     * @param Collection<T> $collection
     *
     * @return Collection<T>
     */
    public function merge(Collection $collection): Collection;

    /**
     * @param class-string<F> $className
     *
     * @return self<F>
     *
     * @template F of object
     */
    public function filter(string $className): Collection;

    /**
     * @param callable(T):bool $callback
     *
     * @return self<T>
     */
    public function matches(callable $callback): Collection;

    /**
     * @param class-string<C> $classString
     * @param array<C> $elements
     *
     * @return self<C>
     *
     * @template C
     */
    public static function fromClassString(string $classString, array $elements = []): Collection;

    /**
     * @param class-string<C> $interfaceString
     * @param array<C> $elements
     *
     * @return self<C>
     *
     * @template C
     */
    public static function fromInterfaceString(string $interfaceString, array $elements = []): Collection;
}
