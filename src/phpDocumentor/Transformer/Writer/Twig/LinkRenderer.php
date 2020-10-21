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
final class LinkRenderer
{
    public const PRESENTATION_NORMAL = 'normal';
    public const PRESENTATION_URL = 'url';
    public const PRESENTATION_CLASS_SHORT = 'class:short';
    public const PRESENTATION_FILE_SHORT = 'file:short';

    /** @var string */
    private $destination = '';

    /** @var Router */
    private $router;

    /** @var ProjectDescriptor|null */
    private $project;

    /** @var bool */
    private $convertToRootPath = true;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @deprecated in favour of withDestination()
     */
    public function setDestination(string $destination) : void
    {
        $this->destination = $destination;
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
    public function withDestination(string $destination) : self
    {
        $result = clone $this;
        $result->destination = $destination;

        return $result;
    }

    public function withProject(ProjectDescriptor $projectDescriptor) : self
    {
        $result = clone $this;
        $result->project = $projectDescriptor;

        return $result;
    }

    public function doNotConvertUrlsToRootPath() : self
    {
        $result = clone $this;
        $result->convertToRootPath = false;

        return $result;
    }

    /**
     * Returns the target directory relative to the Project's Root.
     */
    public function getDestination() : string
    {
        return $this->destination;
    }

    /**
     * @param Descriptor|Fqsen|Uri $value
     */
    public function link(object $value) : string
    {
        $uri = $this->router->generate($value);
        if (!$uri) {
            return $uri;
        }

        return $this->convertToRootPath($this->withoutLeadingSlash($uri));
    }

    /**
     * @param array<Type>|Type|DescriptorAbstract|Fqsen|Reference\Reference|Path|string|iterable<mixed> $value
     *
     * @return string[]|string
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
     * Converts the given path to be relative to the root of the documentation
     * target directory.
     *
     * It is not possible to use absolute paths in documentation templates since
     * they may be used locally, or in a subfolder. As such we need to calculate
     * the number of levels to go up from the current document's directory and
     * then append the given path.
     *
     * For example:
     *
     *     Suppose you are in <root>/classes/my/class.html and you want open
     *     <root>/my/index.html then you provide 'my/index.html' to this method
     *     and it will convert it into ../../my/index.html (<root>/classes/my is
     *     two nesting levels until the root).
     *
     * This method does not try to normalize or optimize the paths in order to
     * save on development time and performance, and because it adds no real
     * value.
     *
     * In addition, when a path starts with an @-sign, it is interpreted as a
     * reference to a structural element and we use the router to try and find
     * a path to which this refers.
     *
     * @todo References can only point to an element that is a class,
     *       interface, trait, method, property or class constant at this
     *       moment. This is because an FQSEN does not contain the necessary
     *       data to distinguish whether the FQCN is actually a class or a
     *       namespace reference. As such we assume a class as that is the
     *       most common occurrence.
     */
    public function convertToRootPath(string $pathOrReference, bool $force = false) : ?string
    {
        if ($this->isReferenceToFqsen($pathOrReference)) {
            try {
                $pathOrReference = $this->router->generate($this->createFqsenFromReference($pathOrReference));
            } catch (InvalidArgumentException $e) {
                return null;
            }
        }

        if (!$pathOrReference) {
            return null;
        }

        $withoutLeadingSlash = $this->withoutLeadingSlash($pathOrReference);
        if ($this->convertToRootPath || $force) {
            return $this->getPathPrefixBasedOnDepth() . $withoutLeadingSlash;
        }

        return $withoutLeadingSlash;
    }

    /**
     * Returns a series of anchors and strings for the given collection of routable items.
     *
     * @param iterable<mixed> $value
     *
     * @return list<string>
     */
    private function renderASeriesOfLinks(iterable $value, string $presentation) : array
    {
        $result = [];
        foreach ($value as $path) {
            $result[] = $this->render($path, $presentation);
        }

        return $result;
    }

    /**
     * @param string|Path|Type|DescriptorAbstract|Fqsen|Reference\Fqsen $node
     */
    private function renderLink($node, string $presentation) : string
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
            $node = $node->getFqsen() ?: $node;
        }

        if ($node instanceof Fqsen) {
            $node = $this->project->findElement($node) ?: $node;
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

        try {
            if ($url !== false && UriInfo::isRelativePath(Uri::createFromString($url))) {
                $url = $this->convertToRootPath($url);
            }
        } catch (SyntaxError $exception) {
            // do nothing; the url apparently is not valid enough; let's just pass it on
        }

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
    private function renderType(iterable $value) : array
    {
        $result = [];
        foreach ($value as $type) {
            $result[] = (string) $type;
        }

        return $result;
    }

    /**
     * Calculates how deep the given destination is and returns a prefix.
     *
     * The calculated prefix is used to get back to the root (i.e. three levels deep means `../../..`) or an empty
     * string is returned when you are already at the same level as the root.
     *
     * This prefix will include a trailing forward slash (/) when it actually needs to direct the caller to go
     * elsewhere.
     */
    private function getPathPrefixBasedOnDepth() : string
    {
        $directoryDepth = substr_count($this->getDestination(), '/') + 1;

        return $directoryDepth > 1
            ? implode('/', array_fill(0, $directoryDepth - 1, '..')) . '/'
            : '';
    }

    private function isReferenceToFqsen(string $path) : bool
    {
        return strpos($path, '@') === 0;
    }

    private function withoutLeadingSlash(string $path) : string
    {
        return ltrim($path, '/');
    }

    private function createFqsenFromReference(string $path) : Fqsen
    {
        if (!$this->isReferenceToFqsen($path)) {
            throw new InvalidArgumentException('References to FQSENs are expected to begin with an @-sign');
        }

        $strippedAtSign = substr($path, 1);

        // Ensure it is prefixed with a \; as without it it cannot be a valid FQSEN
        if ($strippedAtSign[0] !== '\\') {
            $strippedAtSign = '\\' . $strippedAtSign;
        }

        return new Fqsen($strippedAtSign);
    }

    private function renderAbstractListLinks(AbstractList $node, string $presentation) : string
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
