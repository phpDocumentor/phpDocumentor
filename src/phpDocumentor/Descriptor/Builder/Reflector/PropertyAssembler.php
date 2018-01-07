<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2018 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Builder\Reflector;

use phpDocumentor\Descriptor\PropertyDescriptor;
use phpDocumentor\Reflection\ClassReflector\PropertyReflector;
use phpDocumentor\Reflection\Php\Property;

/**
 * Assembles a PropertyDescriptor from a PropertyReflector.
 */
class PropertyAssembler extends AssemblerAbstract
{
    /**
     * Creates a Descriptor from the provided data.
     *
     * @param Property $data
     *
     * @return PropertyDescriptor
     */
    public function create($data)
    {
        $propertyDescriptor = new PropertyDescriptor();
        $propertyDescriptor->setNamespace(substr($data->getFqsen(), 0, -strlen($data->getName()) - 3));
        $propertyDescriptor->setFullyQualifiedStructuralElementName($data->getFqsen());
        $propertyDescriptor->setName($data->getName());
        $propertyDescriptor->setVisibility($data->getVisibility() ?: 'public');
        $propertyDescriptor->setStatic($data->isStatic());
        $propertyDescriptor->setDefault($data->getDefault());

        $this->assembleDocBlock($data->getDocBlock(), $propertyDescriptor);
        $propertyDescriptor->setLine($data->getLocation()->getLineNumber());

        return $propertyDescriptor;
    }
}
