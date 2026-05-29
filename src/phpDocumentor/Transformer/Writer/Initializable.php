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

namespace phpDocumentor\Transformer\Writer;

use phpDocumentor\Descriptor\Interfaces\DocumentationSetInterface;
use phpDocumentor\Descriptor\Interfaces\ProjectInterface;
use phpDocumentor\Transformer\Template;

interface Initializable
{
    public function initialize(
        ProjectInterface $project,
        DocumentationSetInterface $documentationSet,
        Template $template,
    ): void;
}
