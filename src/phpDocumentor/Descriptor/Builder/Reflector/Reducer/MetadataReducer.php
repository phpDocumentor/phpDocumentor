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

namespace phpDocumentor\Descriptor\Builder\Reflector\Reducer;

use phpDocumentor\Descriptor\Builder\AssemblerReducer;
use phpDocumentor\Descriptor\Descriptor;
use phpDocumentor\Descriptor\Interfaces\MetadataAwareInterface;
use phpDocumentor\Reflection\Metadata\MetaDataContainer;

final class MetadataReducer implements AssemblerReducer
{
    public function create(object $data, Descriptor|null $descriptor = null): Descriptor|null
    {
        if ($data instanceof MetaDataContainer === false) {
            return $descriptor;
        }

        if ($descriptor instanceof MetadataAwareInterface === false) {
            return $descriptor;
        }

        $descriptor->setMetadata($data->getMetadata());

        return $descriptor;
    }
}
