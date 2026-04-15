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

namespace phpDocumentor\Configuration;

use phpDocumentor\FileSystem\Path;

class TemplateDefinition
{
    /** @param array<string, mixed> $parameters */
    public function __construct(
        public string $name,
        public Path|null $location = null,
        public array $parameters = [],
    ) {
    }
}
