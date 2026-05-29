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

namespace phpDocumentor\Descriptor\Interfaces\TableOfContents;

use phpDocumentor\Descriptor\Interfaces\Collection as CollectionInterface;

interface Entry
{
    public function getUrl(): string;

    public function getTitle(): string;

    public function getParent(): string|null;

    /** @return CollectionInterface<Entry> */
    public function getChildren(): CollectionInterface;
}
