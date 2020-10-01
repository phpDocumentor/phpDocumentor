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

/**
 * @deprecated
 */
class DeciderReference extends Reference
{
    public function getName() : string
    {
        return 'decider';
    }

    public function resolve(Environment $environment, string $data) : ResolvedReference
    {
        return new ResolvedReference(
            $environment->getCurrentFileName(),
            $data,
            '#'
        );
    }
}
