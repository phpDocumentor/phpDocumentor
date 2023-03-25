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

namespace phpDocumentor\Transformer\View;

use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Transformer\Transformation;

interface ViewSet
{
    public static function create(
        ProjectDescriptor $project,
        DocumentationSetDescriptor $documentationSet,
        Transformation $transformation
    ): self;

    /**
     * Returns all views that can be exposed to writers.
     *
     * @return array<string, mixed>
     */
    public function getViews(): array;
}
