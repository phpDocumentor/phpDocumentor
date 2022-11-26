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

namespace phpDocumentor\Descriptor\Tag\BaseTypes;

use phpDocumentor\Descriptor\IsTyped;
use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Descriptor\Traits\CanHaveAType;

/**
 * Base descriptor for tags that have a type associated with them.
 */
abstract class TypedAbstract extends TagDescriptor implements IsTyped
{
    use CanHaveAType;
}
