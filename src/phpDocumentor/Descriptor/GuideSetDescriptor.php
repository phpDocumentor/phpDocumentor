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

namespace phpDocumentor\Descriptor;

use phpDocumentor\Configuration\Source;

final class GuideSetDescriptor extends DocumentationSetDescriptor
{
    /** @var string */
    private $inputFormat;

    /** @var string */
    private $outputFormat;

    /** @var Collection<DocumentDescriptor> */
    private $documents;

    /** @var int */
    private $initialHeaderLevel;

    public function __construct(
        string $name,
        Source $source,
        string $outputLocation,
        string $inputFormat,
        string $outputFormat = 'html',
        int $initialHeaderLevel = 1
    ) {
        parent::__construct();

        $this->name = $name;
        $this->source = $source;
        $this->outputLocation = $outputLocation;
        $this->inputFormat = $inputFormat;
        $this->documents = Collection::fromClassString(DocumentDescriptor::class);
        $this->outputFormat = $outputFormat;
        $this->initialHeaderLevel = $initialHeaderLevel;
    }

    public function addDocument(string $file, DocumentDescriptor $documentDescriptor): void
    {
        $this->documents->set($file, $documentDescriptor);
    }

    public function getInputFormat(): string
    {
        return $this->inputFormat;
    }

    public function getOutputFormat(): string
    {
        return $this->outputFormat;
    }

    public function getInitialHeaderLevel(): int
    {
        return $this->initialHeaderLevel;
    }

    /** @return Collection<DocumentDescriptor> */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }
}
