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

use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\NodeRenderers\NodeRendererFactory;

interface Format
{
    public function getFileExtension(): string;

    public function getNodeRendererFactory(Environment $environment): NodeRendererFactory;
}
