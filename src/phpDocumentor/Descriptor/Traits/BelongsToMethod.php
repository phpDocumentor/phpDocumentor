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

use phpDocumentor\Descriptor\Interfaces\MethodInterface;

trait BelongsToMethod
{
    protected ?MethodInterface $method = null;

    /**
     * To which method does this argument belong to
     */
    public function setMethod(MethodInterface $method): void
    {
        $this->method = $method;
    }

    public function getMethod(): ?MethodInterface
    {
        return $this->method;
    }
}
