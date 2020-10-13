<?php

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

declare(strict_types=1);

namespace phpDocumentor\Guides;

final class Files implements \IteratorAggregate
{
    /** @var string[] */
    private $files = [];

    public function add(string $filename) : void
    {
        if (in_array($filename, $this->files, true)) {
            return;
        }

        $this->files[] = $filename;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->files);
    }
}
