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

namespace phpDocumentor\Descriptor\Traits;

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\Interfaces\AttributeInterface;
use phpDocumentor\Descriptor\Interfaces\Collection as CollectionInterface;

trait HasAttributes
{
    /** @var CollectionInterface<AttributeInterface> $attributes Attributes set on this class. */
    protected CollectionInterface $attributes;

    /** @internal should not be called by any other class than the assamblers */
    public function addAttribute(AttributeInterface $attribute): void
    {
        if (! isset($this->attributes)) {
            $this->attributes = Collection::fromInterfaceString(AttributeInterface::class);
        }

        $this->getAttributes()->add($attribute);
    }

    /** @return CollectionInterface<AttributeInterface> */
    public function getAttributes(): CollectionInterface
    {
        if (! isset($this->attributes)) {
            $this->attributes = Collection::fromInterfaceString(AttributeInterface::class);
        }

        return $this->attributes;
    }
}
