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

namespace phpDocumentor\Descriptor\Interfaces\DocBlock;

use phpDocumentor\Descriptor\TagDescriptor;

interface DescriptionInterface
{
    public function getBodyTemplate(): string;

    public function replaceTag(int $position, TagInterface|null $tagDescriptor): void;

    /**
     * Returns the tags for this description
     *
     * @return array<int, TagDescriptor|null>
     */
    public function getTags(): array;

    public function isEmpty(): bool;
}
