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
use phpDocumentor\Descriptor\Interfaces\ElementInterface;
use phpDocumentor\Descriptor\Traits\HasNamespace;
use phpDocumentor\Descriptor\Traits\HasPackage;
use phpDocumentor\Reflection\Fqsen;

final class ApiSetDescriptor extends DocumentationSetDescriptor
{
    use HasPackage;
    use HasNamespace;

    public function __construct(
        string $name,
        Source $source,
        string $outputLocation,
        private readonly ApiSpecification $apiSpecification,
    ) {
        $this->name = $name;
        $this->source = $source;
        $this->outputLocation = $outputLocation;

        parent::__construct();

        $namespace = new NamespaceDescriptor();
        $namespace->setName('\\');
        $namespace->setFullyQualifiedStructuralElementName(new Fqsen('\\'));
        $this->setNamespace($namespace);

        $package = new PackageDescriptor();
        $package->setName('\\');
        $package->setFullyQualifiedStructuralElementName(new Fqsen('\\'));
        $this->setPackage($package);

        // Pre-initialize elements index
        $this->getIndexes()['elements'] = Collection::fromInterfaceString(ElementInterface::class);
    }

    public function getSettings(): ApiSpecification
    {
        return $this->apiSpecification;
    }

    /**
     * Finds a structural element with the given FQSEN in this Documentation Set, or returns null when it
     * could not be found.
     */
    public function findElement(Fqsen $fqsen): ElementInterface|null
    {
        if (! isset($this->getIndexes()['elements'])) {
            return null;
        }

        return $this->getIndexes()['elements']->fetch((string) $fqsen);
    }
}
