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

namespace phpDocumentor\Guides\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use function sprintf;

class AssetsExtension extends AbstractExtension
{
    public function getFunctions() : array
    {
        return [
            new TwigFunction('asset', [$this, 'asset'], ['is_safe' => ['html']]),
        ];
    }

    public function asset(string $path) : string
    {
        return sprintf('/assets/%s', $path);
    }
}
