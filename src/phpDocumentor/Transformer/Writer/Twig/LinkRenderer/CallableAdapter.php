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
use phpDocumentor\Reflection\Types\Callable_;
use phpDocumentor\Reflection\Types\Intersection;
use phpDocumentor\Transformer\Writer\Twig\LinkRendererInterface;

use function assert;
use function implode;
use function is_array;
use function sprintf;
use function trim;

final class CallableAdapter implements LinkRendererInterface
{
    public function __construct(private readonly LinkRendererInterface $rendererChain)
    {
    }

    /** @param mixed $value */
    public function supports($value): bool
    {
        return $value instanceof Callable_;
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    public function render($value, string $presentation)
    {
        if ($this->supports($value) === false) {
            throw new InvalidArgumentException('The given value is not supported by this adapter');
        }

        // the above would already assert this, but phpstan and phpstorm need this
        assert($value instanceof Callable_);

        if ($value->getReturnType() === null && $value->getParameters() === []) {
            return 'callable';
        }

        $parameters = [];
        foreach ($value->getParameters() as $parameter) {
            $type = $this->renderType($parameter->getType(), $presentation);
            $extraInfo = [];
            $name = '';

            if ($parameter->isVariadic()) {
                $extraInfo[] = '...';
            }

            if ($parameter->isReference()) {
                $extraInfo[] = '&';
            }

            if ($parameter->getName() !== null) {
                $name = '$' . $parameter->getName();
            }

            $parameters[] = sprintf(
                '%s%s%s%s%s',
                $type,
                $parameter->getName() !== null ? ' ' : '',
                implode('', $extraInfo),
                $name,
                $parameter->isOptional() ? '=' : '',
            );
        }

        $returnType = $value->getReturnType() !== null
            ? ': ' . $this->renderType($value->getReturnType(), $presentation)
            : '';

        return trim(sprintf('callable(%s)%s', implode(', ', $parameters), $returnType));
    }

    private function renderType(Type $type, string $presentation): string
    {
        $rendered = $this->rendererChain->render($type, $presentation);
        if (! is_array($rendered)) {
            return $rendered;
        }

        $separator = $type instanceof Intersection ? '&' : '|';

        return implode($separator, $rendered);
    }
}
