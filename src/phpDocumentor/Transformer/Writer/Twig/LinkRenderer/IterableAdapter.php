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

use InvalidArgumentException;
use phpDocumentor\Transformer\Writer\Twig\LinkRendererInterface;

use function array_merge;
use function is_array;
use function is_iterable;

final class IterableAdapter implements LinkRendererInterface
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
        return is_iterable($value);
    }

    /**
     * {@inheritDoc}
     */
    public function render($value, string $presentation)
    {
        if ($this->supports($value) === false) {
            throw new InvalidArgumentException('The given value is not supported by this adapter');
        }

        return $this->renderASeriesOfLinks($value, $presentation);
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
