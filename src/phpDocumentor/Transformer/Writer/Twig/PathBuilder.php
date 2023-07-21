<?php

declare(strict_types=1);

namespace phpDocumentor\Transformer\Writer\Twig;

use League\Uri\Uri;
use phpDocumentor\Descriptor\Descriptor;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Transformer\Router\Router;
use Webmozart\Assert\Assert;

use function ltrim;

final class PathBuilder
{
    public function __construct(
        private readonly Router $router,
        private readonly RelativePathToRootConverter $relativePathToRootConverter,
    ) {
    }

    /** @param Descriptor|Fqsen|Uri $value */
    public function link(object $value): string
    {
        Assert::isInstanceOfAny($value, [Descriptor::class, Fqsen::class, Uri::class]);

        return $this->withoutLeadingSlash($this->router->generate($value));
    }

    /** @param Descriptor|Fqsen|Uri $value */
    public function resolvedLink(string $destination, object $value): string
    {
        $uri = $this->link($value);
        if (! $uri) {
            return $uri;
        }

        $path = $this->relativePathToRootConverter->convert($destination, $uri);

        Assert::notNull($path);

        return $path;
    }

    private function withoutLeadingSlash(string $path): string
    {
        return ltrim($path, '/');
    }
}
