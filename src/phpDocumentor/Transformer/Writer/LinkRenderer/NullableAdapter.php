<?php

declare(strict_types=1);

namespace phpDocumentor\Transformer\Writer\LinkRenderer;

use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Null_;
use phpDocumentor\Reflection\Types\Nullable;
use phpDocumentor\Transformer\Writer\Twig\LinkRendererInterface;

final class NullableAdapter implements LinkRendererInterface
{
    private LinkRendererInterface $rendererChain;

    public function __construct(LinkRendererInterface $rendererChain)
    {
        $this->rendererChain = $rendererChain;
    }

    public function supports($value): bool
    {
        return $value instanceof Nullable;
    }

    public function render($value, string $presentation)
    {
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
