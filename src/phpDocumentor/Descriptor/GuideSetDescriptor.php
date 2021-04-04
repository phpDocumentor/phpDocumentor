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

use phpDocumentor\Configuration\Source;

final class GuideSetDescriptor extends DocumentationSetDescriptor
{
    /** @var string */
    private $inputFormat;

    public function __construct(string $name, Source $source, string $output, string $inputFormat)
    {
        $this->name = $name;
        $this->source = $source;
        $this->output = $output;
        $this->inputFormat = $inputFormat;
    }

    public function getInputFormat() : string
    {
        return $this->inputFormat;
    }
}
