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

use phpDocumentor\Configuration\ApiSpecification;
use phpDocumentor\Configuration\Source;

final class ApiSetDescriptor extends DocumentationSetDescriptor
{
    /** @var Collection<FileDescriptor> */
    private $files;

    /** @var Collection<NamespaceDescriptor> */
    private $namespaces;

    /** @var ApiSpecification */
    private $apiSpecification;

    public function __construct(
        string $name,
        Source $source,
        string $outputLocation,
        Collection $files,
        Collection $namespaces,
        ApiSpecification $apiSpecification
    ) {
        parent::__construct();
        $this->name = $name;
        $this->source = $source;
        $this->outputLocation = $outputLocation;
        $this->files = $files;
        $this->namespaces = $namespaces;
        $this->apiSpecification = $apiSpecification;
    }

    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function getNamespaces(): Collection
    {
        return $this->namespaces;
    }

    public function getSettings(): ApiSpecification
    {
        return $this->apiSpecification;
    }
}
