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
use phpDocumentor\FileSystem\FileSystem;
use Twig\Error\LoaderError;
use Twig\Loader\LoaderInterface;
use Twig\Source;

use function rtrim;
use function sprintf;
use function str_starts_with;
use function strlen;
use function substr;

final class FileSystemLoader implements LoaderInterface
{
    /**
     * @var string|null prefix used to allow extends of base templates. For example
     *  `{% extends 'template::css/template.css.twig' %}`
     */
    private $overloadPrefix;

    public function __construct(
        private readonly FileSystem $filesystem,
        private readonly string $templatePath = '',
        string|null $overloadPrefix = null,
    ) {
        $this->overloadPrefix = $overloadPrefix !== null ? $overloadPrefix . '::' : null;
    }

    public function getSourceContext(string $name): Source
    {
        $this->guardTemplateExistsAndIsFile($name);

        $path = $this->resolveTemplateName($name);

        $code = $this->filesystem->read($path);
        if ($code === false) {
            throw new InvalidArgumentException('Could not read file ' . $path);
        }

        return new Source(
            $code,
            $name,
            $path,
        );
    }

    public function exists(string $name): bool
    {
        return $this->filesystem->has($this->resolveTemplateName($name));
    }

    /**
     * Gets the cache key to use for the cache for a given template name.
     *
     * Simple straightforward implementation that checks the existence of the template, and if it exists: returns the
     * name to be used as a cache key.
     *
     * @throws LoaderError When $name is not found.
     *
     * @inheritDoc
     */
    public function getCacheKey(string $name): string
    {
        $this->guardTemplateExistsAndIsFile($name);

        return $name;
    }

    /** @inheritDoc */
    public function isFresh(string $name, $time): bool
    {
        $this->guardTemplateExistsAndIsFile($name);

        $timestamp = $this->filesystem->lastModified($this->resolveTemplateName($name));

        return (int) $time >= $timestamp;
    }

    /** @throws LoaderError */
    private function guardTemplateExistsAndIsFile(string $name): void
    {
        $path = $this->resolveTemplateName($name);
        if (! $this->filesystem->has($path)) {
            throw new LoaderError(sprintf("File '%s' does not exist", $path));
        }
    }

    private function resolveTemplateName(string $name): string
    {
        if (($this->overloadPrefix !== null) && str_starts_with($name, $this->overloadPrefix)) {
            $name = substr($name, strlen($this->overloadPrefix));
        }

        $prefix = $this->templatePath;
        if ($prefix !== '') {
            $prefix = rtrim($prefix, '/') . '/';
        }

        return $prefix . $name;
    }
}
