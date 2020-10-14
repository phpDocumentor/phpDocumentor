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
use function sprintf;
use function strtolower;

class PhpClassReference extends Reference
{
    public function getName() : string
    {
        return 'phpclass';
    }

    public function resolve(Environment $environment, string $data) : ResolvedReference
    {
        return new ResolvedReference(
            $environment->getCurrentFileName(),
            $data,
            sprintf('%s/class.%s.php', '', strtolower($data)),
            [],
            ['title' => $data]
        );
    }
}
