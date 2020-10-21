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

use phpDocumentor\Guides\Formats\Format;
use RuntimeException;
use function sprintf;

final class Configuration
{
    /** @var string */
    private $baseUrl = '';

    /** @var callable|null */
    private $baseUrlEnabledCallable;

    /** @var bool */
    private $ignoreInvalidReferences = false;

    /** @var int */
    private $initialHeaderLevel = 1;

    /** @var string */
    private $fileExtension = Format::HTML;

    /** @var string */
    private $indexName = 'index';

    /** @var string */
    private $sourceFileExtension = 'rst';

    /** @var Format[] */
    private $formats;

    /** @var string */
    private $outputFolder = '';

    /**
     * @param iterable<Format> $outputFormats
     */
    public function __construct(iterable $outputFormats)
    {
        $this->addFormat(...$outputFormats);
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

    public function getInitialHeaderLevel() : int
    {
        return $this->initialHeaderLevel;
    }

    public function setInitialHeaderLevel(int $initialHeaderLevel) : void
    {
        $this->initialHeaderLevel = $initialHeaderLevel;
    }

    public function getFileExtension() : string
    {
        return $this->fileExtension;
    }

    public function setFileExtension(string $fileExtension) : void
    {
        $this->fileExtension = $fileExtension;
    }

    public function getFormat() : Format
    {
        if (!isset($this->formats[$this->fileExtension])) {
            throw new RuntimeException(
                sprintf('Format %s does not exist.', $this->fileExtension)
            );
        }

        return $this->formats[$this->fileExtension];
    }

    public function addFormat(Format ...$format) : void
    {
        foreach ($format as $item) {
            $this->formats[$item->getFileExtension()] = $item;
        }
    }

    public function getNameOfIndexFile() : string
    {
        return $this->indexName;
    }

    public function getSourceFileExtension() : string
    {
        return $this->sourceFileExtension;
    }

    public function getOutputFolder() : string
    {
        return $this->outputFolder;
    }

    public function setOutputFolder(string $outputFolder) : void
    {
        $this->outputFolder = $outputFolder;
    }
}
