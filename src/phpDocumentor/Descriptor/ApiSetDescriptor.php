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
use phpDocumentor\Reflection\Fqsen;

final class ApiSetDescriptor extends DocumentationSetDescriptor
{
    private ApiSpecification $apiSpecification;

    public function __construct(
        string $name,
        Source $source,
        string $outputLocation,
        ApiSpecification $apiSpecification
    ) {
        $this->name = $name;
        $this->source = $source;
        $this->outputLocation = $outputLocation;
        $this->apiSpecification = $apiSpecification;

        parent::__construct();

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
    public function findElement(Fqsen $fqsen): ?ElementInterface
    {
        if (!isset($this->getIndexes()['elements'])) {
            return null;
        }

        return $this->getIndexes()['elements']->fetch((string) $fqsen);
    }

    public function getApiSpecification(): ApiSpecification
    {
        return $this->apiSpecification;
    }
}
