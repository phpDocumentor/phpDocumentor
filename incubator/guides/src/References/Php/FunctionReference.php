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

namespace phpDocumentor\Guides\References\Php;

use phpDocumentor\Guides\References\ResolvedReference;
use phpDocumentor\Guides\RenderContext;
use RuntimeException;

/**
 * @link https://docs.readthedocs.io/en/stable/guides/cross-referencing-with-sphinx.html
 */
final class FunctionReference extends Reference
{
    public function getName(): string
    {
        return 'func';
    }

    public function resolve(RenderContext $environment, string $data): ResolvedReference
    {
        throw new RuntimeException(
            'Not supported until Guides can read the API Documentation TOC because '
            . 'functions do not have their own files but are documented in their file\'s document.'
        );
    }
}
