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
use phpDocumentor\Reflection\PseudoTypes\Generic;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Intersection;
use phpDocumentor\Transformer\Writer\Twig\LinkRendererInterface;

use function assert;
use function implode;
use function is_array;

final class GenericAdapter implements LinkRendererInterface
{
    public function __construct(private readonly LinkRendererInterface $rendererChain)
    {
    }

    /** @param mixed $value */
    public function supports($value): bool
    {
        return $value instanceof Generic;
    }

    /** @param mixed $value */
    public function render($value, string $presentation): string
    {
        if ($this->supports($value) === false) {
            throw new InvalidArgumentException('The given value is not supported by this adapter');
        }

        assert($value instanceof Generic);

        $genericName = $value->getFqsen() === null
            ? 'object'
            : $this->rendererChain->render($value->getFqsen(), $presentation);

        $types = [];
        foreach ($value->getTypes() as $type) {
            $types[] = $this->renderType($type, $presentation);
        }

        return $genericName . '&lt;' . implode(', ', $types) . '&gt;';
    }

    private function renderType(Type $type, string $presentation): string
    {
        $rendered = $this->rendererChain->render($type, $presentation);
        if (! is_array($rendered)) {
            return $rendered;
        }

        return implode($type instanceof Intersection ? '&' : '|', $rendered);
    }
}
