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

use phpDocumentor\Configuration\ApiSpecification;
use phpDocumentor\Configuration\Source;

final class ApiSetDescriptor extends DocumentationSetDescriptor
{
    /** @var ApiSpecification */
    private $apiSpecification;

    public function __construct(string $name, Source $source, string $output, ApiSpecification $apiSpecification)
    {
        parent::__construct();
        $this->name = $name;
        $this->source = $source;
        $this->output = $output;
        $this->apiSpecification = $apiSpecification;
    }

    public function getSettings(): ApiSpecification
    {
        return $this->apiSpecification;
    }
}
