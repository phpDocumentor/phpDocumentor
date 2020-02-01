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

namespace phpDocumentor\Descriptor;

use phpDocumentor\Dsn;

final class GuideSetDescriptor extends DocumentationSetDescriptor
{
    /**
     * @param array<Dsn|list<string>> $source
     *
     * @phpstan-param array{dsn: Dsn, paths: array<int, string>} $source
     */
    public function __construct(string $name, array $source, string $output)
    {
        $this->name = $name;
        $this->source = $source;
        $this->output = $output;
    }
}
