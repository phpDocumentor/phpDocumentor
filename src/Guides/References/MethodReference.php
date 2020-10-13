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

namespace phpDocumentor\Guides\References;

use phpDocumentor\Guides\Environment;
use RuntimeException;

class MethodReference extends Reference
{
    public function getName() : string
    {
        return 'method';
    }

    public function resolve(Environment $environment, string $data) : ResolvedReference
    {
        $className = explode('::', $data)[0];
        $className = str_replace('\\\\', '\\', $className);

        if (false === strpos($data, '::')) {
            throw new RuntimeException(
                sprintf('Malformed method reference  "%s" in file "%s"', $data, $environment->getCurrentFileName())
            );
        }

        $methodName = explode('::', $data)[1];

        return new ResolvedReference(
            $environment->getCurrentFileName(),
            $methodName . '()',
            sprintf('%s/%s.html#method_%s', '', str_replace('\\', '/', $className), $methodName),
            [],
            [
                'title' => sprintf('%s::%s()', $className, $methodName),
            ]
        );
    }
}
