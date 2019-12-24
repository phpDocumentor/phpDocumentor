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
use const E_USER_DEPRECATED;
use function array_filter;
use function trigger_error;

/**
 * Base descriptor for tags that have a type associated with them.
 */
abstract class TypedAbstract extends TagDescriptor
{
    /** @var Type $type */
    protected $type;

    public function __construct($name, ?Type $type)
    {
        parent::__construct($name);
        $this->type = $type;
    }

    /**
     * Sets a list of types associated with this tag.
     */
    public function setType(?Type $types = null) : void
    {
        $this->type = $types;
    }


    /**
     * Returns the list of types associated with this tag.
     */
    public function getTypes() : array
    {
        trigger_error('Use getType, because type is an object', E_USER_DEPRECATED);
        return array_filter([$this->type]);
    }

    public function getType() : ?Type
    {
        return $this->type;
    }
}
