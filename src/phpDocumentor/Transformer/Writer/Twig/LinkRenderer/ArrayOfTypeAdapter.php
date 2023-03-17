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
use phpDocumentor\Reflection\Type;
use phpDocumentor\Transformer\Writer\Twig\LinkRendererInterface;

use function current;
use function is_array;

/**
 * Converts an array with types into a string-based representation of these types.
 *
 * @todo How and why is this adapter used? The assumption in the supports method is weird
 *       and that this adapter involves arrays is oddly specific. Is this a potential bug?
 */
final class ArrayOfTypeAdapter implements LinkRendererInterface
{
    /**
     * {@inheritDoc}
     */
    public function supports($value): bool
    {
        return is_array($value) && current($value) instanceof Type;
    }

    /**
     * {@inheritDoc}
     */
    public function render($value, string $presentation)
    {
        if ($this->supports($value) === false) {
            throw new InvalidArgumentException('The given value is not supported by this adapter');
        }

        /** @var array<Type> $value Assuming every element of iterable is similar */
        return $this->renderType($value);
    }

    /**
     * @param iterable<Type> $value
     *
     * @return list<string>
     */
    private function renderType(iterable $value): array
    {
        $result = [];
        foreach ($value as $type) {
            $result[] = (string) $type;
        }

        return $result;
    }
}
