<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Writer\Twig;

use InvalidArgumentException;
use League\Uri\Exceptions\SyntaxError;
use League\Uri\Uri;
use League\Uri\UriInfo;
use phpDocumentor\Descriptor\Descriptor;
use phpDocumentor\Path;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Transformer\Router\Router;
use const DIRECTORY_SEPARATOR;
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
use function substr;

/**
 * Renders an HTML anchor pointing to the location of the provided element.
 */
final class LinkRenderer
{
    public const PRESENTATION_NORMAL = 'normal';
    public const PRESENTATION_URL = 'url';
    public const PRESENTATION_CLASS_SHORT = 'class:short';

    /** @var string */
    private $destination = '';

    /** @var Router */
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
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
     *
     * @see \phpDocumentor\Transformer\Writer\Twig for the invocation
     *     of this method.
     */
    public function setDestination(string $destination) : void
    {
        $this->destination = $destination;
    }

    /**
     * Returns the target directory relative to the Project's Root.
     */
    public function getDestination() : string
    {
        return $this->destination;
    }

    public function link($value) : string
    {
        $uri = $this->router->generate($value);
        if (!$uri) {
            return $uri;
        }

        return $this->convertToRootPath($uri);
    }

    /**
     * @param Type[]|Descriptor|Fqsen|Path|string|iterable $value
     *
     * @return string[]|string
     */
    public function render($value, string $presentation)
    {
        if (is_array($value) && current($value) instanceof Type) {
            return $this->renderType($value);
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
    public function convertToRootPath(string $pathOrReference) : ?string
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

        return $this->getPathPrefixBasedOnDepth() . $this->withoutLeadingSlash($pathOrReference);
    }

    /**
     * Returns a series of anchors and strings for the given collection of routable items.
     *
     * @return string[]
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
     * @param string|Path|Descriptor|Fqsen $node
     */
    private function renderLink($node, string $presentation) : string
    {
        $generatedUrl = $node;

        if (is_string($node)) {
            try {
                $node = new Fqsen($node);
            } catch (InvalidArgumentException $exception) {
                // do nothing; apparently this was not an FQSEN
            }
        }

        if ($node instanceof Descriptor || $node instanceof Fqsen) {
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
            case self::PRESENTATION_URL: // return the first url
                return $url ?: '';
            case self::PRESENTATION_CLASS_SHORT:
                $parts = explode('\\', (string) $node);
                $node = end($parts);
                break;
        }

        return $url ? sprintf('<a href="%s">%s</a>', $url, (string) $node) : (string) $node;
    }

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
        $directoryDepth = count(explode(DIRECTORY_SEPARATOR, $this->getDestination()));

        return $directoryDepth > 1
            ? implode('/', array_fill(0, $directoryDepth - 1, '..')) . '/'
            : '';
    }

    private function isReferenceToFqsen(string $path) : bool
    {
        return $path[0] === '@';
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
}
