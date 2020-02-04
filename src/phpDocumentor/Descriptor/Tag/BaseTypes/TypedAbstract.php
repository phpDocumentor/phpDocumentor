<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Tag\BaseTypes;

use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Reflection\Type;
use function array_filter;
use function trigger_error;
use const E_USER_DEPRECATED;

/**
 * Base descriptor for tags that have a type associated with them.
 */
abstract class TypedAbstract extends TagDescriptor
{
    /** @var Type $types */
    protected $types;

    /**
     * Sets a list of types associated with this tag.
     */
    public function setType(?Type $types = null) : void
    {
        $this->types = $types;
    }

    public function getType() : ?Type
    {
        return $this->types;
    }
}
