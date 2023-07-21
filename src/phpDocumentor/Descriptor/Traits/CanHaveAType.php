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

namespace phpDocumentor\Descriptor\Traits;

use phpDocumentor\Descriptor\Interfaces\InheritsFromElement;
use phpDocumentor\Descriptor\IsTyped;
use phpDocumentor\Reflection\Type;

use function array_filter;
use function trigger_error;

use const E_USER_DEPRECATED;

trait CanHaveAType
{
    /** @var Type|null $type normalized type of this argument */
    protected Type|null $type = null;

    public function setType(Type|null $type): void
    {
        $this->type = $type;
    }

    public function getType(): Type|null
    {
        if ($this->type === null && $this instanceof InheritsFromElement) {
            $inheritedElement = $this->getInheritedElement();
            if ($inheritedElement instanceof IsTyped) {
                $this->setType($inheritedElement->getType());
            }
        }

        return $this->type;
    }

    /** @return list<Type> */
    public function getTypes(): array
    {
        trigger_error('Please use getType', E_USER_DEPRECATED);

        return array_filter([$this->getType()]);
    }
}
