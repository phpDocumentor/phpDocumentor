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

namespace phpDocumentor\Guides\NodeRenderers\Html;

use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\NodeRenderers\NodeRenderer;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\TocNode;
use phpDocumentor\Guides\ReferenceBuilder;
use phpDocumentor\Guides\Renderer;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Webmozart\Assert\Assert;

use function count;
use function is_array;

class TocNodeRenderer implements NodeRenderer
{
    /** @var Renderer */
    private $renderer;

    /** @var ReferenceBuilder */
    private $referenceRegistry;

    public function __construct(Renderer $renderer, ReferenceBuilder $referenceRegistry)
    {
        $this->renderer = $renderer;
        $this->referenceRegistry = $referenceRegistry;
    }

    public function render(Node $node, Environment $environment): string
    {
        Assert::isInstanceOf($node, TocNode::class);

        if ($node->getOption('hidden', false)) {
            return '';
        }

        $tocItems = [];

        foreach ($node->getFiles() as $file) {
            $reference = $this->referenceRegistry->resolve(
                $environment,
                'doc',
                $file,
                $environment->getMetaEntry()
            );

            if ($reference === null) {
                continue;
            }

            $url = $environment->relativeUrl($reference->getUrl());

            $this->buildLevel($environment, $node, $url, $reference->getTitles(), 1, $tocItems);
        }

        return $this->renderer->render(
            'toc.html.twig',
            [
                'tocNode' => $node,
                'tocItems' => $tocItems,
            ]
        );
    }

    /**
     * @param mixed[][] $titles
     * @param mixed[][] $tocItems
     */
    private function buildLevel(
        Environment $environment,
        TocNode $node,
        ?string $url,
        array $titles,
        int $level,
        array &$tocItems
    ): void {
        foreach ($titles as $entry) {
            [$title, $children] = $entry;

            [$title, $target] = $this->generateTarget($environment, $url, $title);

            $tocItem = [
                'targetId' => $this->generateTargetId($target),
                'targetUrl' => $environment->generateUrl($target),
                'title' => $title,
                'level' => $level,
                'children' => [],
            ];

            // render children until we hit the configured maxdepth
            if (count($children) > 0 && $level < $node->getDepth()) {
                $this->buildLevel($environment, $node, $url, $children, $level + 1, $tocItem['children']);
            }

            $tocItems[] = $tocItem;
        }
    }

    private function generateTargetId(string $target): string
    {
        return (new AsciiSlugger())->slug($target)->lower()->toString();
    }

    /**
     * @param string[]|string $title
     *
     * @return array{mixed, string}
     */
    private function generateTarget(Environment $environment, ?string $url, $title): array
    {
        $anchor = $this->generateAnchorFromTitle($title);

        $target = $url . '#' . $anchor;

        if (is_array($title)) {
            [$title, $target] = $title;

            $reference = $this->referenceRegistry->resolve(
                $environment,
                'doc',
                $target,
                $environment->getMetaEntry()
            );

            if ($reference === null) {
                return [$title, $target];
            }

            $target = $environment->relativeUrl($reference->getUrl());
        }

        return [$title, $target];
    }

    /**
     * @param string[]|string $title
     */
    private function generateAnchorFromTitle($title): string
    {
        $slug = is_array($title)
            ? $title[1]
            : $title;

        return (new AsciiSlugger())->slug($slug)->lower()->toString();
    }

    public function supports(Node $node): bool
    {
        return $node instanceof TocNode;
    }
}
