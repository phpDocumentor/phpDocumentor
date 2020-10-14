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

namespace phpDocumentor\Guides;

use League\Uri\UriInfo;
use phpDocumentor\Transformer\Router\Router;
use phpDocumentor\UriFactory;
use Symfony\Component\Routing\Generator\UrlGenerator as SymfonyUrlGenerator;
use function implode;
use function ltrim;
use function rtrim;

final class UrlGenerator
{
    /** @var Configuration */
    private $configuration;

    /** @var Router */
    private $router;

    public function __construct(Configuration $configuration, Router $router)
    {
        $this->configuration = $configuration;
        $this->router = $router;
    }

    public function generateUrl(string $path, string $currentFileName, string $dirName): string
    {
        $uri = UriFactory::createUri($path);
        if (UriInfo::isAbsolute($uri)) {
            return $path;
        }

        return $this->relativeUrl($path);
    }

    public function absoluteUrl(string $dirName, string $url): string
    {
        $uri = UriFactory::createUri($url);
        if (UriInfo::isAbsolute($uri)) {
            return $url;
        }

        // $url is a relative path so join it together with the
        // current $dirName to produce an absolute url
        return rtrim($dirName, '/') . '/' . $url;
    }

    /**
     * Resolves a relative URL using directories, for instance, if the
     * current directory is "path/to/something", and you want to get the
     * relative URL to "path/to/something/else.html", the result will
     * be else.html. Else, "../" will be added to go to the upper directory
     */
    public function relativeUrl(?string $url): ?string
    {
        return SymfonyUrlGenerator::getRelativePath($this->configuration->getOutputFolder(), $url);
    }

    public function canonicalUrl(string $dirName, string $url): ?string
    {
        return $this->router->generate(UriFactory::createUri($url));
    }

    private function getPathPrefixBasedOnDepth(): string
    {
        $directoryDepth = substr_count($this->configuration->getOutputFolder(), '/') + 1;

        return $directoryDepth > 1
            ? implode('/', array_fill(0, $directoryDepth - 1, '..')) . '/'
            : '';
    }

    private function withoutLeadingSlash(string $path): string
    {
        return ltrim($path, '/');
    }
}
