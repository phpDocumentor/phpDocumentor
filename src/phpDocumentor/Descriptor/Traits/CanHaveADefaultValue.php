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

trait CanHaveADefaultValue
{
    /** @var string|null $default the default value for an argument or null if none is provided */
    protected string|null $default = null;

    public function setDefault(string|null $value): void
    {
        $this->default = $value;
    }

    public function getDefault(): string|null
    {
        return $this->default;
    }
}
