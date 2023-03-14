<?php

declare(strict_types=1);

namespace phpDocumentor\Transformer\Writer\LinkRenderer;

use phpDocumentor\Reflection\Type;
use phpDocumentor\Transformer\Writer\Twig\LinkRendererInterface;

final class TypeAdapter implements LinkRendererInterface
{
    public function supports($value): bool
    {
        return is_array($value) && current($value) instanceof Type;
    }

    public function render($value, string $presentation)
    {
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
