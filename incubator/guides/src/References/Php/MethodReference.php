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
use RuntimeException;

use function explode;
use function sprintf;
use function str_replace;
use function strpos;

/**
 * @link https://www.sphinx-doc.org/en/master/usage/restructuredtext/domains.html#python-roles
 */
final class MethodReference extends Reference
{
    public function getName(): string
    {
        return 'meth';
    }

    public function resolve(Environment $environment, string $data): ResolvedReference
    {
        // TODO: The location of the resolved method or class should come from the TOC and not like this

        $className = explode('::', $data)[0];
        $className = str_replace('\\\\', '\\', $className);

        if (strpos($data, '::') === false) {
            throw new RuntimeException(
                sprintf('Malformed method reference  "%s" in file "%s"', $data, $environment->getCurrentFileName())
            );
        }

        $methodName = explode('::', $data)[1];
        $classPath = sprintf('%s/classes/%s.html', '', str_replace('\\', '-', $data));

        return new ResolvedReference(
            $environment->getCurrentFileName(),
            $methodName . '()',
            sprintf('%s.html#method_%s', $classPath, $methodName),
            [],
            [
                'title' => sprintf('%s::%s()', $className, $methodName),
            ]
        );
    }
}
