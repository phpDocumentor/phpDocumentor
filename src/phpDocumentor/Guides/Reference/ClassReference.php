<?php

declare(strict_types=1);

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

class ClassReference extends Reference
{
    public function getName() : string
    {
        return 'class';
    }

    public function resolve(Environment $environment, string $data) : ResolvedReference
    {
        $className = str_replace('\\\\', '\\', $data);

        return new ResolvedReference(
            $environment->getCurrentFileName(),
            substr(strrchr($className, '\\'), 1),
            sprintf('%s/%s.html', '', str_replace('\\', '/', $className)),
            [],
            [
                'title' => $className,
            ]
        );
    }
}
