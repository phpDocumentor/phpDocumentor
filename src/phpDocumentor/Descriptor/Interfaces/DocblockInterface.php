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

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\TagDescriptor;

interface DocblockInterface
{
    /**
     * Sets a summary describing this element.
     */
    public function setSummary(string $summary): void;

    /**
     * Returns the summary describing this element.
     */
    public function getSummary(): string;

    /**
     * Returns all tags associated with this element.
     *
     * @return Collection<Collection<TagDescriptor>>
     */
    public function getTags(): Collection;
}
