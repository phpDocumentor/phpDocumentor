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
use phpDocumentor\Transformer\Router\Router;
use phpDocumentor\Transformer\Writer\Twig\LinkRenderer;
use Webmozart\Assert\Assert;

use function is_string;
use function ltrim;
use function sprintf;
use function str_starts_with;
use function strlen;
use function substr;

/**
 * Url generator for rendering links in the twig templates.
 *
 * @todo Why is this a separate URL Generator on top of the Router, specific for the LinkRenderer? Are we missing a
 *       concept? At the moment, I am not going into this because my PR is getting too big already and I keep existing
 *       behaviour, but discovering why this Url Generator even exists could improve the codebase.
 */
class UrlGenerator
{
    public function __construct(private readonly LinkRenderer $rendererChain, private readonly Router $router)
    {
    }

    /** @param string|Path|Type|DescriptorAbstract|Fqsen|Reference\Reference|Reference\Fqsen $target */
    public function generate($target, string $fallback): string|null
    {
        $unlinkable = $target instanceof Fqsen || $target instanceof Type;
        if ($unlinkable) {
            // With an unlinkable object, we don't know if the page for it exists; so we don't render a link to it.
            return null;
        }

        if ($this->isGuideUrl($target)) {
            return $this->generateGuideUrl($target);
        }

        if (! $target instanceof Descriptor) {
            return $fallback;
        }

        try {
            $url = $this->router->generate($target);
        } catch (InvalidArgumentException) {
            return null;
        }

        return $this->withoutLeadingSlash($url);
    }

    /** @param string|Reference\Url $target */
    private function generateGuideUrl($target): string|null
    {
        if ((is_string($target) || $target instanceof Reference\Url) === false) {
            throw new InvalidArgumentException(
                'Guide references can only be derived from a string or Url reference',
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
        $documentEntry = $guideSet->getGuidesProjectNode()->findDocumentEntry($guideReference);
        if ($documentEntry === null) {
            return null;
        }

        try {
            $document = $guideSet->getDocuments()->get($documentEntry->getFile());
        } catch (OutOfRangeException) {
            return null;
        }

        return sprintf(
            '%s/%s',
            $guideSet->getOutputLocation(),
            // TODO: Add Support for DocumentEntries to the router
            $this->withoutLeadingSlash($this->router->generate($document)),
        );
    }

    /** @param string|Reference\Url $target */
    private function isGuideUrl($target): bool
    {
        if ($target instanceof Reference\Url) {
            $target = (string) $target;
        }

        return is_string($target) && str_starts_with($target, 'doc://');
    }

    private function withoutLeadingSlash(string $path): string
    {
        return ltrim($path, '/');
    }
}
