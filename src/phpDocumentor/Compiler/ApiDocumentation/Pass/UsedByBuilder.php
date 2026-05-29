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

namespace phpDocumentor\Compiler\ApiDocumentation\Pass;

use phpDocumentor\Compiler\ApiDocumentation\ApiDocumentationPass;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\Interfaces\ApiDocumentationSet;
use phpDocumentor\Descriptor\Interfaces\DocBlock\TagInterface;
use phpDocumentor\Descriptor\Interfaces\ElementInterface;
use phpDocumentor\Descriptor\Tag\UsedByDescriptor;
use phpDocumentor\Descriptor\Tag\UsesDescriptor;
use phpDocumentor\Pipeline\Attribute\Stage;

#[Stage(
    'phpdoc.pipeline.api_documentation.compile',
    9000,
    'Creates a link for uses tags on the counter side',
)]
final class UsedByBuilder extends ApiDocumentationPass
{
    protected function process(ApiDocumentationSet $subject): ApiDocumentationSet
    {
        foreach ($subject->getIndexes()->get('elements') as $element) {
            $uses = $element->getTags()->fetch('uses', Collection::fromInterfaceString(TagInterface::class));
            foreach ($uses->filter(UsesDescriptor::class) as $usesTag) {
                $counterSide = $usesTag->getReference();
                if ($counterSide instanceof ElementInterface === false) {
                    continue;
                }

                $tag = new UsedByDescriptor('used-by', $usesTag->getDescription());
                $tag->setReference($element);
                $counterSide->getTags()->fetch(
                    'used-by',
                    Collection::fromInterfaceString(TagInterface::class),
                )->add($tag);
            }
        }

        return $subject;
    }
}
