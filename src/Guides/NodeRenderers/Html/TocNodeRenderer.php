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

use InvalidArgumentException;
use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\NodeRenderers\NodeRenderer;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\TocNode;
use Symfony\Component\String\Slugger\AsciiSlugger;

use function count;
use function is_array;

class TocNodeRenderer implements NodeRenderer
{
    /** @var Environment */
    private $environment;

    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
    }

    public function render(Node $node): string
    {
        if ($node instanceof TocNode === false) {
            throw new InvalidArgumentException('Invalid node presented');
        }

        if ($node->getOption('hidden', false)) {
            return '';
        }

        $tocItems = [];

        foreach ($node->getFiles() as $file) {
            $reference = $this->environment->resolve('doc', $file);

            if ($reference === null) {
                continue;
            }

            $url = $this->environment->relativeUrl($reference->getUrl());

            $this->buildLevel($node, $url, $reference->getTitles(), 1, $tocItems);
        }

        return $this->environment->getRenderer()->render(
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
        TocNode $node,
        ?string $url,
        array $titles,
        int $level,
        array &$tocItems
    ): void {
        foreach ($titles as $entry) {
            [$title, $children] = $entry;

            [$title, $target] = $this->generateTarget($url, $title);

            $tocItem = [
                'targetId' => $this->generateTargetId($target),
                'targetUrl' => $this->environment->generateUrl($target),
                'title' => $title,
                'level' => $level,
                'children' => [],
            ];

            // render children until we hit the configured maxdepth
            if (count($children) > 0 && $level < $node->getDepth()) {
                $this->buildLevel($node, $url, $children, $level + 1, $tocItem['children']);
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
    private function generateTarget(?string $url, $title): array
    {
        $anchor = $this->generateAnchorFromTitle($title);

        $target = $url . '#' . $anchor;

        if (is_array($title)) {
            [$title, $target] = $title;

            $reference = $this->environment->resolve('doc', $target);

            if ($reference === null) {
                return [$title, $target];
            }

            $target = $this->environment->relativeUrl($reference->getUrl());
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
}
