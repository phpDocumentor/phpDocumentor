<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\References;

use phpDocumentor\Guides\RestructuredText\Environment;
use phpDocumentor\Guides\RestructuredText\Meta\MetaEntry;

class Resolver
{
    /**
     * @param string[] $attributes
     */
    public function resolve(
        Environment $environment,
        string $data,
        array $attributes = []
    ) : ?ResolvedReference {
        $resolvedFileReference = $this->resolveFileReference($environment, $data, $attributes);

        if ($resolvedFileReference !== null) {
            return $resolvedFileReference;
        }

        $resolvedAnchorReference = $this->resolveAnchorReference($environment, $data, $attributes);

        if ($resolvedAnchorReference !== null) {
            return $resolvedAnchorReference;
        }

        return null;
    }

    /**
     * @param string[] $attributes
     */
    private function resolveFileReference(
        Environment $environment,
        string $data,
        array $attributes = []
    ) : ?ResolvedReference {
        $entry = null;

        $file = $environment->canonicalUrl($data);

        if ($file !== null) {
            $entry = $environment->getMetas()->get($file);
        }

        if ($entry === null) {
            return null;
        }

        return $this->createResolvedReference($file, $environment, $entry, $attributes);
    }

    /**
     * @param string[] $attributes
     */
    private function resolveAnchorReference(
        Environment $environment,
        string $data,
        array $attributes = []
    ) : ?ResolvedReference {
        $entry = $environment->getMetas()->findLinkMetaEntry($data);

        if ($entry !== null) {
            return $this->createResolvedReference($entry->getFile(), $environment, $entry, $attributes, $data);
        }

        return null;
    }

    /**
     * @param string[] $attributes
     */
    private function createResolvedReference(
        ?string $file,
        Environment $environment,
        MetaEntry $entry,
        array $attributes = [],
        ?string $anchor = null
    ) : ResolvedReference {
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
