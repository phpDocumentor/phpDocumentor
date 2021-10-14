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

use phpDocumentor\Guides\References\Reference as BaseReference;

/**
 * @link https://docs.readthedocs.io/en/stable/guides/cross-referencing-with-sphinx.html
 * @link https://www.sphinx-doc.org/en/master/usage/restructuredtext/domains.html
 */
abstract class Reference extends BaseReference
{
    public function getDomain(): string
    {
        return 'php';
    }
}
