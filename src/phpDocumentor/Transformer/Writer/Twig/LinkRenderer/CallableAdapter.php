<?php

declare(strict_types=1);

namespace phpDocumentor\Transformer\Writer\Twig\LinkRenderer;

use InvalidArgumentException;
use phpDocumentor\Reflection\Types\Callable_;
use phpDocumentor\Transformer\Writer\Twig\LinkRendererInterface;

use function assert;
use function implode;
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
            $type = $this->rendererChain->render($parameter->getType(), $presentation);
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

        return trim(sprintf(
            'callable(%s)%s',
            implode(', ', $parameters),
            $value->getReturnType() !== null ? ': ' . $this->rendererChain->render(
                $value->getReturnType(),
                $presentation,
            ) : '',
        ));
    }
}
