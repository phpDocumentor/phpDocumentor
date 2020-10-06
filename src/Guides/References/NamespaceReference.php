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

namespace phpDocumentor\Guides\References;

use phpDocumentor\Guides\Environment;

class NamespaceReference extends Reference
{
    public function getName() : string
    {
        return 'namespace';
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
