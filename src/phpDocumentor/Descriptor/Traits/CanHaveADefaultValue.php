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

use phpDocumentor\Reflection\Php\Expression;

trait CanHaveADefaultValue
{
    /** @var Expression|null $default the default value for an argument or null if none is provided */
    protected Expression|null $default = null;

    public function setDefault(Expression|null $value): void
    {
        $this->default = $value;
    }

    public function getDefault(): Expression|null
    {
        return $this->default;
    }
}
