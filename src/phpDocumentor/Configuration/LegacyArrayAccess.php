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

namespace phpDocumentor\Configuration;

use InvalidArgumentException;
use ReturnTypeWillChange;

use function lcfirst;
use function property_exists;
use function str_replace;
use function ucwords;

trait LegacyArrayAccess
{
    /** @param string $offset */
    #[ReturnTypeWillChange]
    public function offsetExists($offset): bool
    {
        $property = $this->normalizePropertyName($offset);

        return property_exists($this, $property);
    }

    /**
     * @param string $offset
     *
     * @return mixed
     */
    #[ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        $property = $this->normalizePropertyName($offset);
        if (!property_exists($this, $property)) {
            throw new InvalidArgumentException('Invalid property ' . $property);
        }

        return $this->$property;
    }

    /**
     * @param string $offset
     * @param mixed $value
     */
    #[ReturnTypeWillChange]
    public function offsetSet($offset, $value): void
    {
        $property = $this->normalizePropertyName($offset);
        if (!property_exists($this, $property)) {
            throw new InvalidArgumentException('Invalid property ' . $property);
        }

        $this->{$property} = $value;
    }

    /** @param string $offset */
    #[ReturnTypeWillChange]
    public function offsetUnset($offset): void
    {
        $property = $this->normalizePropertyName($offset);
        if (!property_exists($this, $property)) {
            throw new InvalidArgumentException('Invalid property ' . $property);
        }

        $this->$property = null;
    }

    private function normalizePropertyName(string $offset): string
    {
        return lcfirst(str_replace('-', '', ucwords($offset, '-')));
    }
}
