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

namespace phpDocumentor\Compiler\Version\Pass\TableOfContentsBuilder;

use phpDocumentor\Descriptor\Interfaces\DocumentationSetInterface;

/** @template TDocumentationSet of DocumentationSetInterface */
interface DocumentationSetBuilder
{
    public function supports(DocumentationSetInterface $documentationSet): bool;

    /** @phpstan-param TDocumentationSet $documentationSet */
    public function build(DocumentationSetInterface $documentationSet): void;
}
