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

namespace phpDocumentor\Transformer\Writer\Twig\LinkRenderer;

use phpDocumentor\Reflection\Types\Null_;
use phpDocumentor\Reflection\Types\Nullable;
use phpDocumentor\Transformer\Writer\Twig\LinkRendererInterface;
use Webmozart\Assert\Assert;

use function array_merge;
use function is_array;

final class NullableAdapter implements LinkRendererInterface
{
    private LinkRendererInterface $rendererChain;

    public function __construct(LinkRendererInterface $rendererChain)
    {
        $this->rendererChain = $rendererChain;
    }

    /**
     * {@inheritDoc}
     */
    public function supports($value): bool
    {
        return $value instanceof Nullable;
    }

    /**
     * {@inheritDoc}
     */
    public function render($value, string $presentation)
    {
        Assert::isInstanceOf($value, Nullable::class);

        return $this->renderASeriesOfLinks([$value->getActualType(), new Null_()], $presentation);
    }

    /**
     * Returns a series of anchors and strings for the given collection of routable items.
     *
     * @param iterable<mixed> $value
     *
     * @return list<string>
     */
    private function renderASeriesOfLinks(iterable $value, string $presentation): array
    {
        $result = [];
        foreach ($value as $path) {
            $links = $this->rendererChain->render($path, $presentation);
            if (!is_array($links)) {
                $links = [$links];
            }

            $result = array_merge($result, $links);
        }

        return $result;
    }
}
