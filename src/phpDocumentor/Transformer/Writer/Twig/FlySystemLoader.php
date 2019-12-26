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

use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use Twig\Error\LoaderError;
use Twig\Loader\LoaderInterface;
use Twig\Source;
use function rtrim;
use function sprintf;

final class FlySystemLoader implements LoaderInterface
{
    /** @var FilesystemInterface */
    private $filesystem;

    /** @var string */
    private $templatePath;

    public function __construct(FilesystemInterface $filesystem, string $templatePath = '')
    {
        $this->filesystem = $filesystem;
        $this->templatePath = $templatePath;
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
     * @param string $name The name of the template to load
     *
     * @return string The cache key
     *
     * @throws LoaderError When $name is not found
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
    private function guardTemplateExistsAndIsFile(string $name) : void
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

    private function resolveTemplateName(string $name) : string
    {
        $prefix = $this->templatePath;
        if ($this->templatePath !== null && $this->templatePath !== '') {
            $prefix = rtrim($prefix, '/') . '/';
        }

        return $prefix . $name;
    }
}
