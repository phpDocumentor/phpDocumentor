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

namespace phpDocumentor\Compiler\Version\Pass;

use phpDocumentor\Compiler\CompilableSubject;
use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use phpDocumentor\Descriptor\VersionDescriptor;

final class TableOfContentsBuilder implements CompilerPassInterface
{
    /** @param iterable<array-key, TableOfContentsBuilder\DocumentationSetBuilder<DocumentationSetDescriptor>> $builders */
    public function __construct(private readonly iterable $builders)
    {
    }

    public function getDescription(): string
    {
        return 'Builds table of contents for documentation sets';
    }

    public function __invoke(CompilableSubject $subject): CompilableSubject
    {
        if ($subject instanceof VersionDescriptor === false) {
            return $subject;
        }

        foreach ($subject->getDocumentationSets() as $documentationSet) {
            foreach ($this->builders as $builder) {
                if (! $builder->supports($documentationSet)) {
                    continue;
                }

                $builder->build($documentationSet);
            }
        }

        return $subject;
    }
}
