<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Router\UrlGenerator;

use phpDocumentor\Descriptor\DescriptorAbstract;

/**
 * Generates relative URLs with elements for use in the generated HTML documentation.
 */
interface UrlGenerator
{
    /**
     * Generates a URL from the given node or returns false if unable.
     *
     * @param string|DescriptorAbstract $node
     *
     * @return string|false
     */
    public function __invoke($node);
}
