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

namespace phpDocumentor\Descriptor;

use phpDocumentor\Dsn;
use phpDocumentor\Path;

final class ApiSetDescriptor extends DocumentationSetDescriptor
{
    /**
     * @param array{dsn: Dsn, paths: array<Path>} $source
     */
    public function __construct(string $name, array $source, string $output)
    {
        $this->name = $name;
        $this->source = $source;
        $this->output = $output;
    }
}
