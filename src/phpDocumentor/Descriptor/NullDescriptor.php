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

namespace phpDocumentor\Descriptor;

use phpDocumentor\Descriptor\Filter\Filterable;

final class NullDescriptor implements Filterable
{
    public function getName() : string
    {
        return '';
    }

    public function getDescription() : ?DocBlock\DescriptionDescriptor
    {
        return null;
    }

    public function setErrors(Collection $errors) : void
    {
    }
}
