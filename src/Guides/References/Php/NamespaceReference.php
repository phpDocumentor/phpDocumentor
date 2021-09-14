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

use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\References\ResolvedReference;

use function sprintf;
use function str_replace;
use function strrchr;
use function strtolower;
use function substr;

/**
 * @link https://docs.readthedocs.io/en/stable/guides/cross-referencing-with-sphinx.html
 */
final class NamespaceReference extends Reference
{
    public function getName(): string
    {
        return 'namespace';
    }

    public function resolve(Environment $environment, string $data): ResolvedReference
    {
        // TODO: The location of the resolved namespace should come from the TOC and not like this
        $className = str_replace('\\\\', '\\', $data);

        return new ResolvedReference(
            $environment->getCurrentFileName(),
            substr(strrchr($className, '\\'), 1),
            sprintf('%s/namespaces/%s.html', '', strtolower(str_replace('\\', '-', $className))),
            [],
            ['title' => $className]
        );
    }
}
