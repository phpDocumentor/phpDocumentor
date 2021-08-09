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

    /** @var Collection<DocumentDescriptor> */
    private $documents;

    public function __construct(string $name, Source $source, string $output, string $inputFormat)
    {
        parent::__construct();
        $this->name = $name;
        $this->source = $source;
        $this->output = $output;
        $this->inputFormat = $inputFormat;
        $this->documents = Collection::fromClassString(DocumentDescriptor::class);
    }

    public function addDocument(string $file, DocumentDescriptor $documentDescriptor): void
    {
        $this->documents->set($file, $documentDescriptor);
    }

    public function getInputFormat(): string
    {
        return $this->inputFormat;
    }

    /** @return Collection<DocumentDescriptor> */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }
}
