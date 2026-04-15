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

namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Descriptor\Collection;

interface AttributedInterface
{
    /** @return Collection<AttributeInterface> */
    public function getAttributes(): Collection;

    public function addAttribute(AttributeInterface $attribute): void;
}
