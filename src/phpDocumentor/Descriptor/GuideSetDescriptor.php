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

final class GuideSetDescriptor extends DocumentationSetDescriptor
{
    public function __construct(string $name, array $source)
    {
        $this->name = $name;
        $this->source = $source;
    }
}
