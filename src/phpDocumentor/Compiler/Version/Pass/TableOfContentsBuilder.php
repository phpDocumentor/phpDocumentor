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
use phpDocumentor\Compiler\Version\Pass\TableOfContentsBuilder\ApiSetBuilder;
use phpDocumentor\Compiler\Version\Pass\TableOfContentsBuilder\DocumentationSetBuilder;
use phpDocumentor\Compiler\Version\Pass\TableOfContentsBuilder\GuideSetBuilder;
use phpDocumentor\Descriptor\VersionDescriptor;
use phpDocumentor\Transformer\Router\Router;

final class TableOfContentsBuilder implements CompilerPassInterface
{
    /** @var array<array-key, DocumentationSetBuilder[]> */
    private array $builders;

    public function __construct(private readonly Router $router)
    {
        $this->builders = [
            new ApiSetBuilder($this->router),
            new GuideSetBuilder($this->router),
        ];
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
                if (!$builder->supports($documentationSet)) {
                    continue;
                }

                $builder->build($documentationSet);
            }
        }

        return $subject;
    }
}
