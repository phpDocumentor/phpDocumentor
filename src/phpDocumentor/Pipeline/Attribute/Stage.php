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

namespace phpDocumentor\Pipeline\Attribute;

use Attribute as BaseAttribute;

#[BaseAttribute(BaseAttribute::TARGET_CLASS)]
final class Stage
{
    public function __construct(
        public readonly string $name,
        public readonly int $priority = 1000,
        public readonly string|null $description = null,
    ) {
    }
}
