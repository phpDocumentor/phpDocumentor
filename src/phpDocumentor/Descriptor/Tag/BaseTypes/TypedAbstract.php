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
    /** @var Type|null $types */
    protected $types;

    /**
     * Sets a list of types associated with this tag.
     *
     * @deprecated
     *
     * @codeCoverageIgnore because deprecated and the error makes phpunit fail
     */
    public function setTypes(?Type $types = null): void
    {
        trigger_error('Use setType, because type is an object', E_USER_DEPRECATED);
        $this->types = $types;
    }

    /**
     * Sets a list of types associated with this tag.
     */
    public function setType(?Type $types = null): void
    {
        $this->types = $types;
    }

    /**
     * Returns the list of types associated with this tag.
     *
     * @deprecated
     *
     * @return list<Type>
     *
     * @codeCoverageIgnore because deprecated and the error makes phpunit fail
     */
    public function getTypes(): array
    {
        trigger_error('Use getType, because type is an object', E_USER_DEPRECATED);

        return array_filter([$this->types]);
    }

    public function getType(): ?Type
    {
        return $this->types;
    }
}
