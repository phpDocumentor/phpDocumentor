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

namespace phpDocumentor\Descriptor\Type;

use phpDocumentor\Descriptor\Interfaces\TypeInterface;

class IntegerDescriptor implements TypeInterface
{
    /**
     * Returns a human-readable name for this type.
     */
    public function getName() : string
    {
        return 'integer';
    }

    /**
     * Returns a human-readable name for this type.
     */
    public function __toString() : string
    {
        return $this->getName();
    }
}
