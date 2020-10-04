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

use phpDocumentor\Descriptor\PropertyDescriptor;
use phpDocumentor\Reflection\Php\Property;
use function strlen;
use function substr;

/**
 * Assembles a PropertyDescriptor from a PropertyReflector.
 *
 * @extends AssemblerAbstract<PropertyDescriptor, Property>
 */
class PropertyAssembler extends AssemblerAbstract
{
    /**
     * Creates a Descriptor from the provided data.
     *
     * @param Property $data
     */
    public function create(object $data) : PropertyDescriptor
    {
        $propertyDescriptor = new PropertyDescriptor();
        $propertyDescriptor->setNamespace(substr((string) $data->getFqsen(), 0, -strlen($data->getName()) - 3));
        $propertyDescriptor->setFullyQualifiedStructuralElementName($data->getFqsen());
        $propertyDescriptor->setName($data->getName());
        $propertyDescriptor->setVisibility((string) $data->getVisibility() ?: 'public');
        $propertyDescriptor->setStatic($data->isStatic());
        $propertyDescriptor->setDefault($this->pretifyValue($data->getDefault()));

        if ($data->getType()) {
            $propertyDescriptor->setType($data->getType());
        }

        $this->assembleDocBlock($data->getDocBlock(), $propertyDescriptor);
        $propertyDescriptor->setLine($data->getLocation()->getLineNumber());

        return $propertyDescriptor;
    }
}
