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

namespace phpDocumentor\Guides\Formats;

use phpDocumentor\Guides\Renderers\NodeRendererFactory;

interface Format
{
    public const HTML = 'html';
    public const LATEX = 'tex';

    public function getFileExtension() : string;

    /**
     * @return NodeRendererFactory[]
     */
    public function getNodeRendererFactories() : array;
}
