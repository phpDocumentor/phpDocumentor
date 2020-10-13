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

namespace phpDocumentor\Guides\Renderers\Html;

use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Nodes\TocNode;
use phpDocumentor\Guides\Renderers\NodeRenderer;
use function count;
use function is_array;

class TocNodeRenderer implements NodeRenderer
{
    /** @var Environment */
    private $environment;

    /** @var TocNode */
    private $tocNode;

    public function __construct(TocNode $tocNode)
    {
        $this->environment = $tocNode->getEnvironment();
        $this->tocNode = $tocNode;
    }

    public function render() : string
    {
        $options = $this->tocNode->getOptions();

        if (isset($options['hidden'])) {
            return '';
        }

        $tocItems = [];

        foreach ($this->tocNode->getFiles() as $file) {
            $reference = $this->environment->resolve('doc', $file);

            if ($reference === null) {
                continue;
            }

            $url = $this->environment->relativeUrl($reference->getUrl());

            $this->buildLevel($url, $reference->getTitles(), 1, $tocItems);
        }

        return $this->tocNode->getEnvironment()->getRenderer()->render(
            'toc.html.twig',
            [
                'tocNode' => $this->tocNode,
                'tocItems' => $tocItems,
            ]
        );
    }

    /**
     * @param mixed[]|array $titles
     * @param mixed[] $tocItems
     */
    private function buildLevel(
        ?string $url,
        array $titles,
        int $level,
        array &$tocItems
    ) : void {
        foreach ($titles as $k => $entry) {
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
            if (count($children) > 0 && $level < $this->tocNode->getDepth()) {
                $this->buildLevel($url, $children, $level + 1, $tocItem['children']);
            }

            $tocItems[] = $tocItem;
        }
    }

    private function generateTargetId(string $target) : string
    {
        return Environment::slugify($target);
    }

    /**
     * @param string[]|string $title
     *
     * @return mixed[]
     */
    private function generateTarget(?string $url, $title) : array
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
    private function generateAnchorFromTitle($title) : string
    {
        $slug = is_array($title)
            ? $title[1]
            : $title;

        return Environment::slugify($slug);
    }
}
