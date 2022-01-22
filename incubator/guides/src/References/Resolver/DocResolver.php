<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\References\Resolver;

use phpDocumentor\Guides\Meta\Entry;
use phpDocumentor\Guides\References\ResolvedReference;
use phpDocumentor\Guides\RenderContext;
use phpDocumentor\Guides\Span\CrossReferenceNode;

final class DocResolver implements Resolver
{
    public function supports(CrossReferenceNode $node, RenderContext $context): bool
    {
        return $node->getRole() === 'doc' || $node->getRole() === 'ref';
    }

    public function resolve(CrossReferenceNode $node, RenderContext $context): ?ResolvedReference
    {
        $filePath = $context->canonicalUrl($node->getUrl());

        if ($filePath === null) {
            return null;
        }

        $entry = $context->getMetas()->get($filePath);
        if ($entry === null) {
            return null;
        }

        return $this->createResolvedReference(
            $filePath,
            $context,
            $entry,
            [],
            $node->getAnchor()
        );
    }

    /**
     * @param string[] $attributes
     *
     * TODO refactor this... I see too many arguments, Why would you use the titles?
     */
    private function createResolvedReference(
        ?string $file,
        RenderContext $environment,
        Entry $entry,
        array $attributes = [],
        ?string $anchor = null
    ): ResolvedReference {
        $url = $entry->getUrl();

        if ($url !== '') {
            $url = $environment->relativeUrl('/' . $url) . ($anchor !== null ? '#' . $anchor : '');
        }

        return new ResolvedReference(
            $file,
            $entry->getTitle(),
            $url,
            $entry->getTitles(),
            $attributes
        );
    }
}
