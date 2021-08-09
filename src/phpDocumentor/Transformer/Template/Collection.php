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

namespace phpDocumentor\Transformer\Template;

use ArrayObject;
//phpcs:ignore SlevomatCodingStandard.Namespaces.UnusedUses.UnusedUse
use phpDocumentor\Transformer\Template;
use phpDocumentor\Transformer\Transformation;

/**
 * Contains a collection of Templates that may be queried.
 *
 * @template-extends ArrayObject<string, Template>
 */
final class Collection extends ArrayObject
{
    /**
     * Returns a list of all transformations contained in the templates of this collection.
     *
     * @return Transformation[]
     */
    public function getTransformations(): array
    {
        $result = [];
        foreach ($this as $template) {
            foreach ($template as $transformation) {
                $result[] = $transformation;
            }
        }

        return $result;
    }
}
