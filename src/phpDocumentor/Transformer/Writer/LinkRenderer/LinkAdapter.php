<?php

declare(strict_types=1);

namespace phpDocumentor\Transformer\Writer\LinkRenderer;

use InvalidArgumentException;
use OutOfRangeException;
use phpDocumentor\Descriptor\Descriptor;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\GuideSetDescriptor;
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
use Webmozart\Assert\Assert;

use function is_string;
use function ltrim;
use function sprintf;

/**
 * Test if an inline reference to guides work too.
 *
 * This should be {@see doc://guides/running-phpdocumentor an inline reference}.
 * This should be {@see \phpDocumentor\Descriptor\Collection an inline reference}.
 *
 * @see doc://guides/running-phpdocumentor
 */
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

        $url = $this->generateUrl($resolvedTarget, (string) $value);

        $unlinkable = $resolvedTarget instanceof Fqsen || $resolvedTarget instanceof Type;

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
        $unlinkable = $target instanceof Fqsen || $target instanceof Type;
        if ($unlinkable) {
            // With an unlinkable object, we don't know if the page for it exists; so we don't render a link to it.
            return null;
        }

        if ($this->isGuideUrl($target)) {
            return $this->generateGuideUrl($target);
        }

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

    private function generateGuideUrl($target): ?string
    {
        Assert::isAnyOf($target, ['string', Reference\Url::class]);

        $guideReference = substr((string) $target, strlen('doc://'));

        // TODO: of course this is not correct, but phpDocumentor does not have a mechanism yet for determining the
        // current version and is prepared for multi-version support but doesn't actually have it yet
        $currentVersion = $this->rendererChain->getProject()->getVersions()->first();
        if ($currentVersion === false) {
            // unlinkable, thus no URL
            return null;
        }

        // TODO: of course this is not correct, but phpDocumentor does not have a mechanism yet for determining the
        // right guide set and it is prepared for multi-guide support but doesn't actually have it yet
        /** @var GuideSetDescriptor|false $guideSet */
        $guideSet = $currentVersion->getDocumentationSets()->filter(GuideSetDescriptor::class)->first();
        if ($guideSet === false) {
            // unlinkable, thus no URL
            return null;
        }

        Assert::isInstanceOf($guideSet, GuideSetDescriptor::class);

        // TODO: This is copied from the TableOfContentsBuilder; once we get proper support for Versions,
        // we need to revisit this.
        $documentEntry = $guideSet->getMetas()->findDocument($guideReference);
        if ($documentEntry === null) {
            return null;
        }

        try {
            $document = $guideSet->getDocuments()->get($documentEntry->getFile());
        } catch (OutOfRangeException $e) {
            return null;
        }

        return sprintf(
            '%s/%s',
            $guideSet->getOutputLocation(),
            // TODO: Add Support for DocumentEntries to the router
            $this->withoutLeadingSlash($this->router->generate($document))
        );
    }

    private function isGuideUrl($target): bool
    {
        if ($target instanceof Reference\Url) {
            $target = (string)$target;
        }
        return is_string($target) && strpos($target, 'doc://') === 0;
    }
}
