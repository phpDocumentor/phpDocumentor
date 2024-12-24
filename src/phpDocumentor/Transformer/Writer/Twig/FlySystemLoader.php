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
use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use Twig\Error\LoaderError;
use Twig\Loader\LoaderInterface;
use Twig\Source;

use function rtrim;
use function sprintf;
use function str_starts_with;
use function strlen;
use function substr;

final class FlySystemLoader implements LoaderInterface
{
    /**
     * @var string|null prefix used to allow extends of base templates. For example
     *  `{% extends 'template::css/template.css.twig' %}`
     */
    private $overloadPrefix;

    public function __construct(
        private readonly FilesystemInterface $filesystem,
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

        $timestamp = $this->filesystem->getTimestamp($this->resolveTemplateName($name));

        return (int) $time >= (int) $timestamp;
    }

    /** @throws LoaderError */
    private function guardTemplateExistsAndIsFile(string $name): void
    {
        try {
            $path = $this->resolveTemplateName($name);
            $metadata = $this->filesystem->getMetadata($path);
            if ($metadata === false) {
                throw new FileNotFoundException($path);
            }

            if ($metadata['type'] !== 'file') {
                throw new LoaderError(
                    sprintf('Cannot use anything other than a file as a template, received: %s', $path),
                );
            }
        } catch (FileNotFoundException) {
            throw new LoaderError(sprintf('Template "%s" could not be found on the given filesystem', $name));
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
