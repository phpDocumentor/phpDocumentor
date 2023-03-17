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
use OutOfRangeException;
use phpDocumentor\Descriptor\Descriptor;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\GuideSetDescriptor;
use phpDocumentor\Path;
use phpDocumentor\Reflection\DocBlock\Tags\Reference;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Transformer\Router\Router;
use phpDocumentor\Transformer\Writer\Twig\LinkRenderer;
use phpDocumentor\Transformer\Writer\Twig\LinkRendererInterface;
use Webmozart\Assert\Assert;

use function is_string;
use function ltrim;
use function sprintf;
use function strlen;
use function strpos;
use function substr;

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
        $resolvedValue = $this->tryToResolveFqsenToDescriptor($value);

        $target = new Target(
            $this->determineTitle($resolvedValue),
            $this->generateUrl($resolvedValue, (string) $value),
            $this->normalizePresentation($resolvedValue, $presentation)
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

    /**
     * @param string|Reference\Url $target
     */
    private function generateGuideUrl($target): ?string
    {
        if ((is_string($target) || $target instanceof Reference\Url) === false) {
            throw new InvalidArgumentException(
                'Guide references can only be derived from a string or Url reference'
            );
        }

        $guideReference = substr((string) $target, strlen('doc://'));

        // TODO: of course this is not correct, but phpDocumentor does not have a mechanism yet for determining the
        // current version and is prepared for multi-version support but doesn't actually have it yet
        $currentVersion = $this->rendererChain->getProject()->getVersions()->first();
        if ($currentVersion === null) {
            // unlinkable, thus no URL
            return null;
        }

        // TODO: of course this is not correct, but phpDocumentor does not have a mechanism yet for determining the
        // right guide set and it is prepared for multi-guide support but doesn't actually have it yet
        /** @var ?GuideSetDescriptor $guideSet */
        $guideSet = $currentVersion->getDocumentationSets()->filter(GuideSetDescriptor::class)->first();
        if ($guideSet === null) {
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

    /**
     * @param string|Reference\Url $target
     */
    private function isGuideUrl($target): bool
    {
        if ($target instanceof Reference\Url) {
            $target = (string) $target;
        }

        return is_string($target) && strpos($target, 'doc://') === 0;
    }

    /**
     * @param string|Path|Type|DescriptorAbstract|Fqsen|Reference\Reference|Reference\Fqsen $resolvedTarget
     */
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

    /**
     * @param string|Path|Type|DescriptorAbstract|Fqsen|Reference\Reference|Reference\Fqsen $resolvedTarget
     */
    private function determineTitle($resolvedTarget): string
    {
        return (string) $resolvedTarget;
    }
}
