<?php

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 * @author Ryan Weaver <ryan@symfonycasts.com> on the original DocBuilder.
 * @author Mike van Riel <me@mikevanriel.com> for adapting this to phpDocumentor.
 */

namespace phpDocumentor\Guides\Reference;

use Doctrine\RST\Environment;
use Doctrine\RST\References\Reference;
use Doctrine\RST\References\ResolvedReference;

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
            [
                'title' => $class,
            ]
        );
    }
}
