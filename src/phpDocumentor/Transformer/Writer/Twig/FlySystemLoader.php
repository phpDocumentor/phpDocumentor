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

use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use Twig\Error\LoaderError;
use Twig\Loader\LoaderInterface;
use Twig\Source;

use function rtrim;
use function sprintf;
use function strlen;
use function strpos;
use function substr;

final class FlySystemLoader implements LoaderInterface
{
    /** @var FilesystemInterface */
    private $filesystem;

    /** @var string */
    private $templatePath;

    /**
     * @var string|null prefix used to allow extends of base templates. For example
     *  `{% extends 'template::css/template.css.twig' %}`
     */
    private $overloadPrefix;

    public function __construct(
        FilesystemInterface $filesystem,
        string $templatePath = '',
        ?string $overloadPrefix = null
    ) {
        $this->filesystem = $filesystem;
        $this->templatePath = $templatePath;
        $this->overloadPrefix = $overloadPrefix !== null ? $overloadPrefix . '::' : null;
    }

    /**
     * @inheritDoc
     */
    public function getSourceContext($name)
    {
        $this->guardTemplateExistsAndIsFile($name);

        $path = $this->resolveTemplateName($name);

        return new Source(
            $this->filesystem->read($path),
            $name,
            $path
        );
    }

    /**
     * @inheritDoc
     */
    public function exists($name)
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
    public function getCacheKey($name)
    {
        $this->guardTemplateExistsAndIsFile($name);

        return $name;
    }

    /**
     * @inheritDoc
     */
    public function isFresh($name, $time)
    {
        $this->guardTemplateExistsAndIsFile($name);

        $timestamp = $this->filesystem->getTimestamp($this->resolveTemplateName($name));

        return (int) $time >= (int) $timestamp;
    }

    /**
     * @throws LoaderError
     */
    private function guardTemplateExistsAndIsFile(string $name): void
    {
        try {
            $path = $this->resolveTemplateName($name);
            $metadata = $this->filesystem->getMetadata($path);
            if ($metadata['type'] !== 'file') {
                throw new LoaderError(
                    sprintf('Cannot use anything other than a file as a template, received: %s', $path)
                );
            }
        } catch (FileNotFoundException $exception) {
            throw new LoaderError(sprintf('Template "%s" could not be found on the given filesystem', $name));
        }
    }

    private function resolveTemplateName(string $name): string
    {
        if (($this->overloadPrefix !== null) && strpos($name, $this->overloadPrefix) === 0) {
            $name = substr($name, strlen($this->overloadPrefix));
        }

        $prefix = $this->templatePath;
        if ($prefix !== '') {
            $prefix = rtrim($prefix, '/') . '/';
        }

        return $prefix . $name;
    }
}
