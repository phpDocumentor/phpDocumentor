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

namespace phpDocumentor\Guides\Renderers;

interface FormatListRenderer
{
    public function createElement(string $text, string $prefix) : string;

    /**
     * @return string[]
     */
    public function createList(bool $ordered) : array;
}
