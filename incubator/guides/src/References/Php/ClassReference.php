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

use function sprintf;
use function str_replace;

/**
 * @link https://docs.readthedocs.io/en/stable/guides/cross-referencing-with-sphinx.html
 * @link https://www.sphinx-doc.org/en/master/usage/restructuredtext/domains.html
 */
final class ClassReference extends Reference
{
    public function getName(): string
    {
        return 'class';
    }

    public function resolve(RenderContext $environment, string $data): ResolvedReference
    {
        // TODO: The location of the resolved class should come from the TOC and not like this
        $classPath = sprintf('%s/classes/%s.html', '', str_replace('\\', '-', $data));

        return new ResolvedReference(
            $environment->getCurrentFileName(),
            $data,
            $classPath,
            [],
            ['title' => $data]
        );
    }
}
