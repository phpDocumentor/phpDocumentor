<?php

declare(strict_types=1);

namespace phpDocumentor\Guides;

use phpDocumentor\Guides\Formats\Format;
use RuntimeException;
use function sprintf;
use function sys_get_temp_dir;

class Configuration
{
    public const THEME_DEFAULT = 'default';

    /** @var string */
    private $cacheDir;

    /** @var string */
    private $theme = self::THEME_DEFAULT;

    /** @var string */
    private $baseUrl = '';

    /** @var callable|null */
    private $baseUrlEnabledCallable;

    /** @var bool */
    private $ignoreInvalidReferences = false;

    /** @var int */
    private $initialHeaderLevel = 1;

    /** @var bool */
    private $useCachedMetas = true;

    /** @var string */
    private $fileExtension = Format::HTML;

    /** @var string */
    private $indexName = 'index';

    /** @var string */
    private $sourceFileExtension = 'rst';

    /** @var TemplateRenderer */
    private $templateRenderer;

    /** @var Format[] */
    private $formats;

    public function __construct()
    {
        $this->cacheDir = sys_get_temp_dir() . '/doctrine-rst-parser';
    }

    public function getCacheDir() : string
    {
        return $this->cacheDir;
    }

    public function setCacheDir(string $cacheDir) : void
    {
        $this->cacheDir = $cacheDir;
    }

    public function getTemplateRenderer() : TemplateRenderer
    {
        return $this->templateRenderer;
    }

    public function setTemplateRenderer(TemplateRenderer $templateRenderer) : void
    {
        $this->templateRenderer = $templateRenderer;
    }

    public function getTheme() : string
    {
        return $this->theme;
    }

    public function setTheme(string $theme) : void
    {
        $this->theme = $theme;
    }

    public function getBaseUrl() : string
    {
        return $this->baseUrl;
    }

    public function setBaseUrl(string $baseUrl) : self
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    public function setBaseUrlEnabledCallable(?callable $baseUrlEnabledCallable) : void
    {
        $this->baseUrlEnabledCallable = $baseUrlEnabledCallable;
    }

    public function getBaseUrlEnabledCallable() : ?callable
    {
        return $this->baseUrlEnabledCallable;
    }

    public function isBaseUrlEnabled(string $path) : bool
    {
        if ($this->baseUrl === '') {
            return false;
        }

        if ($this->baseUrlEnabledCallable !== null) {
            /** @var callable $baseUrlEnabledCallable */
            $baseUrlEnabledCallable = $this->baseUrlEnabledCallable;

            return $baseUrlEnabledCallable($path);
        }

        return true;
    }

    public function getIgnoreInvalidReferences() : bool
    {
        return $this->ignoreInvalidReferences;
    }

    public function setIgnoreInvalidReferences(bool $ignoreInvalidReferences) : void
    {
        $this->ignoreInvalidReferences = $ignoreInvalidReferences;
    }

    public function setInitialHeaderLevel(int $initialHeaderLevel) : void
    {
        $this->initialHeaderLevel = $initialHeaderLevel;
    }

    public function getInitialHeaderLevel() : int
    {
        return $this->initialHeaderLevel;
    }

    public function setUseCachedMetas(bool $useCachedMetas) : void
    {
        $this->useCachedMetas = $useCachedMetas;
    }

    public function getUseCachedMetas() : bool
    {
        return $this->useCachedMetas;
    }

    public function getFileExtension() : string
    {
        return $this->fileExtension;
    }

    public function setFileExtension(string $fileExtension) : void
    {
        $this->fileExtension = $fileExtension;
    }

    public function addFormat(Format $format) : void
    {
        $this->formats[$format->getFileExtension()] = $format;
    }

    public function getFormat() : Format
    {
        if (! isset($this->formats[$this->fileExtension])) {
            throw new RuntimeException(
                sprintf('Format %s does not exist.', $this->fileExtension)
            );
        }

        return $this->formats[$this->fileExtension];
    }

    public function getNameOfIndexFile() : string
    {
        return $this->indexName;
    }

    public function getSourceFileExtension() : string
    {
        return $this->sourceFileExtension;
    }
}
