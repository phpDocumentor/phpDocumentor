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

namespace phpDocumentor\Transformer\Writer\Twig;

use InvalidArgumentException;
use League\Uri\Exceptions\SyntaxError;
use League\Uri\Uri;
use League\Uri\UriInfo;
use phpDocumentor\Descriptor\Descriptor;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Path;
use phpDocumentor\Reflection\DocBlock\Tags\Reference;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\AbstractList;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Collection;
use phpDocumentor\Reflection\Types\Iterable_;
use phpDocumentor\Reflection\Types\Null_;
use phpDocumentor\Reflection\Types\Nullable;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Transformer\Router\Router;
use Webmozart\Assert\Assert;

use function array_fill;
use function array_merge;
use function count;
use function current;
use function end;
use function explode;
use function implode;
use function is_array;
use function is_iterable;
use function is_string;
use function ltrim;
use function sprintf;
use function strpos;
use function substr;
use function substr_count;

/**
 * Renders an HTML anchor pointing to the location of the provided element.
 */
final class LinkRenderer implements LinkRendererInterface
{
    public const PRESENTATION_NORMAL = 'normal';
    public const PRESENTATION_URL = 'url';
    public const PRESENTATION_CLASS_SHORT = 'class:short';
    public const PRESENTATION_FILE_SHORT = 'file:short';

    private string $destination = '';
    private Router $router;
    private ?ProjectDescriptor $project;
    private RelativePathToRootConverter $relativePathToRootConverter;

    public function __construct(
        Router $router,
        RelativePathToRootConverter $relativePathToRootConverter
    ) {
        $this->router = $router;
        $this->relativePathToRootConverter = $relativePathToRootConverter;
    }

    /**
     * Sets the destination directory relative to the Project's Root.
     *
     * The destination is the target directory containing the resulting
     * file. This destination is relative to the Project's root and can
     * be used for the calculation of nesting depths, etc.
     *
     * For this specific extension the destination is provided in the
     * Twig writer itself.
     */
    public function withDestination(string $destination): self
    {
        $result = clone $this;
        $result->destination = $destination;

        return $result;
    }

    public function withProject(ProjectDescriptor $projectDescriptor): self
    {
        $result = clone $this;
        $result->project = $projectDescriptor;

        return $result;
    }

    /**
     * Returns the target directory relative to the Project's Root.
     */
    public function getDestination(): string
    {
        return $this->destination;
    }

    /**
     * @param array<Type>|Type|DescriptorAbstract|Fqsen|Reference\Reference|Path|string|iterable<mixed> $value
     *
     * @return string|list<string>
     */
    public function render($value, string $presentation)
    {
        if (is_array($value) && current($value) instanceof Type) {
            /** @var array<Type> $value Assuming every element of iterable is similar */
            return $this->renderType($value);
        }

        if ($value instanceof Nullable) {
            return $this->renderASeriesOfLinks([$value->getActualType(), new Null_()], $presentation);
        }

        if (is_iterable($value)) {
            return $this->renderASeriesOfLinks($value, $presentation);
        }

        return $this->renderLink($value, $presentation);
    }

    /**
     * Returns a series of anchors and strings for the given collection of routable items.
     *
     * @param iterable<mixed> $value
     *
     * @return list<string>
     */
    private function renderASeriesOfLinks(iterable $value, string $presentation): array
    {
        $result = [];
        foreach ($value as $path) {
            $links = $this->render($path, $presentation);
            if (!is_array($links)) {
                $links = [$links];
            }

            $result = array_merge($result, $links);
        }

        return $result;
    }

    /**
     * @param string|Path|Type|DescriptorAbstract|Fqsen|Reference\Reference|Reference\Fqsen $node
     */
    private function renderLink($node, string $presentation): string
    {
        $generatedUrl = $node;

        if ($node instanceof Reference\Fqsen) {
            $node = (string) $node;
        }

        if (is_string($node)) {
            try {
                $node = new Fqsen($node);
            } catch (InvalidArgumentException $exception) {
                // do nothing; apparently this was not an FQSEN
            }
        }

        if ($node instanceof Object_) {
            $node = $node->getFqsen() ?? $node;
        }

        if ($node instanceof Fqsen) {
            $node = $this->project->findElement($node) ?? $node;
        }

        if ($node instanceof AbstractList) {
            return $this->renderAbstractListLinks($node, $presentation);
        }

        // With an unlinked object, we don't know if the page for it exists; so we don't render a link to it.
        if ($node instanceof Fqsen || $node instanceof Type) {
            // With an unlinked object and the class:short presentation; only show the last bit
            if ($presentation === self::PRESENTATION_CLASS_SHORT && (!$node instanceof Type)) {
                $parts = explode('\\', (string) $node);
                if (count($parts) <= 1) {
                    return (string) $node;
                }

                return sprintf('<abbr title="%s">%s</abbr>', (string) $node, end($parts));
            }

            return (string) $node;
        }

        if ($node instanceof Descriptor) {
            Assert::isInstanceOf($node, DescriptorAbstract::class);
            try {
                $generatedUrl = $this->router->generate($node);
            } catch (InvalidArgumentException $e) {
                $generatedUrl = '';
            }
        }

        $url = $generatedUrl ? ltrim((string) $generatedUrl, '/') : false;

//        try {
//            if ($url !== false && UriInfo::isRelativePath(Uri::createFromString($url))) {
//                $url = $this->relativePathToRootConverter->convert(
//                    $this->getDestination(),
//                    $url
//                );
//            }
//        } catch (SyntaxError $exception) {
//            // do nothing; the url apparently is not valid enough; let's just pass it on
//        }

        switch ($presentation) {
            case self::PRESENTATION_URL:
                // return the first url
                return $url ?: '';

            case self::PRESENTATION_NORMAL:
            case self::PRESENTATION_CLASS_SHORT:
                $parts = explode('\\', (string) $node);

                return sprintf(
                    '<a href="%s"><abbr title="%s">%s</abbr></a>',
                    $url,
                    (string) $node,
                    end($parts)
                );

            case self::PRESENTATION_FILE_SHORT:
                $parts = explode('/', (string) $node);

                return sprintf(
                    '<a href="%s"><abbr title="%s">%s</abbr></a>',
                    $url,
                    (string) $node,
                    end($parts)
                );

            default:
                if ($presentation !== '') {
                    return sprintf(
                        '<a href="%s"><abbr title="%s">%s</abbr></a>',
                        $url,
                        (string) $node,
                        $presentation
                    );
                }
        }

        return $url ? sprintf('<a href="%s">%s</a>', $url, (string) $node) : (string) $node;
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

    private function renderAbstractListLinks(AbstractList $node, string $presentation): string
    {
        $typeLink = null;
        $valueLink = $this->renderLink($node->getValueType(), $presentation);
        $keyLink = $this->renderLink($node->getKeyType(), $presentation);

        if ($node instanceof Collection) {
            $typeLink = $this->renderLink($node->getFqsen(), $presentation);
        }

        if ($node instanceof Array_) {
            $typeLink = 'array';
        }

        if ($node instanceof Iterable_) {
            $typeLink = 'iteratable';
        }

        return sprintf('%s&lt;%s, %s&gt;', $typeLink, $keyLink, $valueLink);
    }
}
