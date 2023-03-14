<?php

declare(strict_types=1);

namespace phpDocumentor\Transformer\Writer\LinkRenderer;

use InvalidArgumentException;
use phpDocumentor\Descriptor\Descriptor;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Path;
use phpDocumentor\Reflection\DocBlock\Tags\Reference;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\AbstractList;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Collection;
use phpDocumentor\Reflection\Types\Iterable_;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Transformer\Router\Router;
use phpDocumentor\Transformer\Writer\Twig\LinkRenderer;
use phpDocumentor\Transformer\Writer\Twig\LinkRendererInterface;

use function is_string;
use function ltrim;
use function sprintf;

final class LinkAdapter implements LinkRendererInterface
{
    private LinkRenderer $rendererChain;
    private Router $router;
    private HtmlFormatter $formatter;

    public function __construct(LinkRenderer $rendererChain, Router $router, HtmlFormatter $formatter)
    {
        $this->rendererChain = $rendererChain;
        $this->router = $router;
        $this->formatter = $formatter;
    }

    /**
     * {@inheritDoc}
     */
    public function supports($value): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function render($value, string $presentation): string
    {
        $resolvedTarget = $this->resolveTarget($value);

        // Iterables with a generic component (lists and collections) should recursively render each part
        if ($resolvedTarget instanceof AbstractList) {
            return $this->renderIterable($resolvedTarget, $presentation);
        }

        $url = null;

        // With an unlinkable object, we don't know if the page for it exists; so we don't render a link to it.
        $unlinkable = $resolvedTarget instanceof Fqsen || $resolvedTarget instanceof Type;
        if ($unlinkable === false) {
            $url = $this->generateUrl($resolvedTarget, (string) $value);
        }

        // With an unlinkable object, we can only use the NORMAL (alias: CLASS_SHORT) or NONE presentation style
        if (
            $unlinkable
            && $presentation !== LinkRenderer::PRESENTATION_CLASS_SHORT
            && $presentation !== LinkRenderer::PRESENTATION_NORMAL
        ) {
            $presentation = LinkRenderer::PRESENTATION_NONE;
        }

        return $this->formatter->formatAs($presentation, (string) $resolvedTarget, $url);
    }

    private function renderIterable(AbstractList $node, string $presentation): string
    {
        $typeLink = null;
        $valueLink = $this->render($node->getValueType(), $presentation);
        $keyLink = $this->render($node->getKeyType(), $presentation);

        if ($node instanceof Collection) {
            $typeLink = $this->render($node->getFqsen(), $presentation);
        }

        if ($node instanceof Array_) {
            $typeLink = 'array';
        }

        if ($node instanceof Iterable_) {
            $typeLink = 'iterable';
        }

        return sprintf('%s&lt;%s, %s&gt;', $typeLink, $keyLink, $valueLink);
    }

    /**
     * @param string|Path|Type|DescriptorAbstract|Fqsen|Reference\Reference|Reference\Fqsen $target
     *
     * @return string|Path|Type|DescriptorAbstract|Fqsen|Reference\Reference|Reference\Fqsen
     */
    private function resolveTarget($target)
    {
        if ($target instanceof Reference\Fqsen) {
            $target = (string) $target;
        }

        if (is_string($target)) {
            try {
                $target = new Fqsen($target);
            } catch (InvalidArgumentException $exception) {
                // do nothing; apparently this was not an FQSEN
            }
        }

        if ($target instanceof Object_) {
            $target = $target->getFqsen() ?? $target;
        }

        if ($target instanceof Fqsen) {
            $target = $this->rendererChain->getProject()->findElement($target) ?? $target;
        }

        return $target;
    }

    /**
     * @param string|Path|Type|DescriptorAbstract|Fqsen|Reference\Reference|Reference\Fqsen $target
     */
    private function generateUrl($target, string $fallback): ?string
    {
        if (!$target instanceof Descriptor) {
            return $fallback;
        }

        try {
            $url = $this->router->generate($target);
        } catch (InvalidArgumentException $e) {
            return null;
        }

        return $this->withoutLeadingSlash($url);
    }

    private function withoutLeadingSlash(string $path): string
    {
        return ltrim($path, '/');
    }
}
