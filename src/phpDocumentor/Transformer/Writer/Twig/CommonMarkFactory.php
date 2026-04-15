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

namespace phpDocumentor\Transformer\Writer\Twig;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Extension\ExtensionInterface;

final class CommonMarkFactory
{
    /** @param iterable<ExtensionInterface> $extensions */
    public function createConverter(iterable $extensions): CommonMarkConverter
    {
        $converter = new CommonMarkConverter([]);
        foreach ($extensions as $extension) {
            $converter->getEnvironment()->addExtension($extension);
        }

        return $converter;
    }
}
