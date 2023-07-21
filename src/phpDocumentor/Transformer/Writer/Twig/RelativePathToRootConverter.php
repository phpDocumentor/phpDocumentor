<?php

declare(strict_types=1);

namespace phpDocumentor\Transformer\Writer\Twig;

use InvalidArgumentException;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Transformer\Router\Router;

use function array_fill;
use function implode;
use function ltrim;
use function str_starts_with;
use function substr;
use function substr_count;

final class RelativePathToRootConverter
{
    public function __construct(private readonly Router $router)
    {
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
    public function convert(string $destination, string $pathOrReference): string|null
    {
        if ($this->isReferenceToFqsen($pathOrReference)) {
            try {
                $pathOrReference = $this->router->generate($this->createFqsenFromReference($pathOrReference));
            } catch (InvalidArgumentException) {
                return null;
            }
        }

        if (! $pathOrReference) {
            return null;
        }

        $withoutLeadingSlash = $this->withoutLeadingSlash($pathOrReference);

        return $this->getPathPrefixBasedOnDepth($destination) . $withoutLeadingSlash;
    }

    private function createFqsenFromReference(string $path): Fqsen
    {
        if (! $this->isReferenceToFqsen($path)) {
            throw new InvalidArgumentException('References to FQSENs are expected to begin with an @-sign');
        }

        $strippedAtSign = substr($path, 1);

        // Ensure it is prefixed with a \; as without it it cannot be a valid FQSEN
        if ($strippedAtSign[0] !== '\\') {
            $strippedAtSign = '\\' . $strippedAtSign;
        }

        return new Fqsen($strippedAtSign);
    }

    private function isReferenceToFqsen(string $path): bool
    {
        return str_starts_with($path, '@');
    }

    private function withoutLeadingSlash(string $path): string
    {
        return ltrim($path, '/');
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
    private function getPathPrefixBasedOnDepth(string $destination): string
    {
        $directoryDepth = substr_count($destination, '/') + 1;

        return $directoryDepth > 1
            ? implode('/', array_fill(0, $directoryDepth - 1, '..')) . '/'
            : '';
    }
}
