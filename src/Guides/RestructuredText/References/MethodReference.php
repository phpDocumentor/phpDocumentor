<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 * @author Ryan Weaver <ryan@symfonycasts.com> on the original DocBuilder.
 * @author Mike van Riel <me@mikevanriel.com> for adapting this to phpDocumentor.
 */

namespace phpDocumentor\Guides\RestructuredText\References;

use phpDocumentor\Guides\RestructuredText\Environment;
use phpDocumentor\Guides\RestructuredText\References\Reference;
use phpDocumentor\Guides\RestructuredText\References\ResolvedReference;
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
