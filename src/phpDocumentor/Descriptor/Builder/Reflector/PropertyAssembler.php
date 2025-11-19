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

namespace phpDocumentor\Descriptor\Builder\Reflector;

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\Interfaces\PropertyInterface;
use phpDocumentor\Descriptor\PropertyDescriptor;
use phpDocumentor\Descriptor\PropertyHookDescriptor;
use phpDocumentor\Descriptor\Tag\VarDescriptor;
use phpDocumentor\Reflection\Php\Property;

use function strlen;
use function substr;

/**
 * Assembles a PropertyDescriptor from a PropertyReflector.
 *
 * @extends AssemblerAbstract<PropertyInterface, Property>
 */
class PropertyAssembler extends AssemblerAbstract
{
    /**
     * Creates a Descriptor from the provided data.
     *
     * @param Property $data
     */
    public function buildDescriptor(object $data): PropertyInterface
    {
        $propertyDescriptor = new PropertyDescriptor();
        $propertyDescriptor->setNamespace(substr((string) $data->getFqsen(), 0, -strlen($data->getName()) - 3));
        $propertyDescriptor->setFullyQualifiedStructuralElementName($data->getFqsen());
        $propertyDescriptor->setName($data->getName());
        $propertyDescriptor->setStatic($data->isStatic());
        $propertyDescriptor->setReadOnly($data->isReadOnly());
        $propertyDescriptor->setDefault($data->getDefault(false));
        $propertyDescriptor->setVirtual($data->isVirtual());

        if ($data->getType()) {
            $propertyDescriptor->setType($data->getType());
        }

        $this->assembleDocBlock($data->getDocBlock(), $propertyDescriptor);
        $propertyDescriptor->setStartLocation($data->getLocation());
        $propertyDescriptor->setEndLocation($data->getEndLocation());
        $this->overwriteTypeFromDocBlock($propertyDescriptor);

        foreach ($data->getHooks() as $hook) {
            $propertyDescriptor->addHook(
                $this->getBuilder()->buildDescriptor($hook, PropertyHookDescriptor::class),
            );
        }

        return $propertyDescriptor;
    }

    private function overwriteTypeFromDocBlock(PropertyInterface $propertyDescriptor): void
    {
        /** @var Collection<VarDescriptor> $varTags */
        $varTags = $propertyDescriptor->getTags()
            ->fetch('var', new Collection())
            ->filter(VarDescriptor::class);

        if ($varTags->count() !== 1) {
            return;
        }

        $propertyDescriptor->setType($varTags[0]->getType());
    }
}
