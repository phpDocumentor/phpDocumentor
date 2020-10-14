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
use function explode;
use function sprintf;
use function strtolower;

class PhpMethodReference extends Reference
{
    public function getName() : string
    {
        return 'phpmethod';
    }

    public function resolve(Environment $environment, string $data) : ResolvedReference
    {
        [$class, $method] = explode('::', $data);

        return new ResolvedReference(
            $environment->getCurrentFileName(),
            $data . '()',
            sprintf('%s/%s.%s.php', '', strtolower($class), strtolower($method)),
            [],
            ['title' => $class]
        );
    }
}
