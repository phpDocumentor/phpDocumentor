<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Builder\Reflector;

use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Reflection\ClassReflector;

/**
 * Assembles an ClassDescriptor using an ClassReflector.
 */
class ClassAssembler extends AssemblerAbstract
{
    /**
     * Creates a Descriptor from the provided data.
     *
     * @param ClassReflector $data
     *
     * @return ClassDescriptor
     */
    public function create($data)
    {
        $classDescriptor = new ClassDescriptor();

        $classDescriptor->setFullyQualifiedStructuralElementName($data->getName());
        $classDescriptor->setName($data->getShortName());
        $classDescriptor->setNamespace('\\' . $data->getNamespace());
        $classDescriptor->setLocation('', $data->getLinenumber());
        $classDescriptor->setParent($data->getParentClass());
        $classDescriptor->setAbstract($data->isAbstract());
        $classDescriptor->setFinal($data->isFinal());

        foreach ($data->getInterfaces() as $interfaceClassName) {
            $classDescriptor->getInterfaces()->set($interfaceClassName, $interfaceClassName);
        }

        $fqcn = $classDescriptor->getFullyQualifiedStructuralElementName();
        $namespace = substr($fqcn, 0, strrpos($fqcn, '\\'));
        $classDescriptor->setNamespace($namespace);


        $this->assembleDocBlock($data, $classDescriptor);

        return $classDescriptor;
    }
}
