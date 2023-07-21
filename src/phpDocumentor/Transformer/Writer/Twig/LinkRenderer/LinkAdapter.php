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
use phpDocumentor\Descriptor\ApiSetDescriptor;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Path;
use phpDocumentor\Reflection\DocBlock\Tags\Reference;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Transformer\Writer\Twig\LinkRenderer;
use phpDocumentor\Transformer\Writer\Twig\LinkRendererInterface;

use function is_string;

/**
 * Renders most of the links to elements, urls and urls with virtual schemes.
 *
 * For more high level information on how references, see {@see doc://internals/rendering/links the documentation}
 * that accompanies this feature.
 *
 * @see doc://hand-written-docs/references how to link back to API elements from hand-written documentation.
 */
final class LinkAdapter implements LinkRendererInterface
{
    public function __construct(
        private readonly LinkRenderer $rendererChain,
        private readonly UrlGenerator $urlGenerator,
        private readonly HtmlFormatter $formatter,
    ) {
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
        $resolvedValue = $this->tryToResolveFqsenToDescriptor($value);

        $target = new Target(
            $this->determineTitle($resolvedValue),
            $this->urlGenerator->generate($resolvedValue, (string) $value),
            $this->normalizePresentation($resolvedValue, $presentation),
        );

        return $this->formatter->format($target);
    }

    /**
     * Some passed types of value are references to an element; let's try to resolve these to a Descriptor.
     *
     * Should we not be able to resolve this to a Descriptor, we return the passed object or an FQSEN
     * that could not be resolved because the element is not in the API docs.
     *
     * @param string|Path|Type|DescriptorAbstract|Fqsen|Reference\Reference|Reference\Fqsen $target
     *
     * @return string|Path|Type|DescriptorAbstract|Fqsen|Reference\Reference
     */
    private function tryToResolveFqsenToDescriptor($target)
    {
        if ($target instanceof Reference\Fqsen) {
            $target = (string) $target;
        }

        if (is_string($target)) {
            try {
                $target = new Fqsen($target);
            } catch (InvalidArgumentException) {
                // do nothing; apparently this was not an FQSEN
            }
        }

        if ($target instanceof Object_) {
            $target = $target->getFqsen() ?? $target;
        }

        $documentationSetDescriptor = $this->rendererChain->getDocumentationSet();
        if ($target instanceof Fqsen && $documentationSetDescriptor instanceof ApiSetDescriptor) {
            $target = $documentationSetDescriptor->findElement($target) ?? $target;
        }

        return $target;
    }

    /** @param string|Path|Type|DescriptorAbstract|Fqsen|Reference\Reference|Reference\Fqsen $resolvedTarget */
    private function normalizePresentation($resolvedTarget, string $presentation): string
    {
        $unlinkable = $resolvedTarget instanceof Fqsen || $resolvedTarget instanceof Type;

        // With an unlinkable object, we can only use the NORMAL (alias: CLASS_SHORT) or NONE presentation style
        if (
            $unlinkable
            && $presentation !== LinkRenderer::PRESENTATION_CLASS_SHORT
            && $presentation !== LinkRenderer::PRESENTATION_NORMAL
        ) {
            $presentation = LinkRenderer::PRESENTATION_NONE;
        }

        return $presentation;
    }

    /** @param string|Path|Type|DescriptorAbstract|Fqsen|Reference\Reference|Reference\Fqsen $resolvedTarget */
    private function determineTitle($resolvedTarget): string
    {
        return (string) $resolvedTarget;
    }
}
