<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Builder\Reflector;

use phpDocumentor\Descriptor\EnumCaseDescriptor;
use phpDocumentor\Descriptor\Interfaces\EnumCaseInterface;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Php\EnumCase;

/**
 * Assembles an EnumCaseDescriptor.
 *
 * @extends AssemblerAbstract<EnumCaseInterface, EnumCase>
 */
final class EnumCaseAssembler extends AssemblerAbstract
{
    /** @param EnumCase $data */
    protected function buildDescriptor(object $data): EnumCaseInterface
    {
        $descriptor = new EnumCaseDescriptor();
        $descriptor->setFullyQualifiedStructuralElementName($data->getFqsen());
        $descriptor->setName($data->getName());
        $descriptor->setStartLocation($data->getLocation());
        $descriptor->setEndLocation($data->getEndLocation());
        $descriptor->setValue($data->getValue(false));
        $this->assembleDocBlock($data->getDocBlock() ?? new DocBlock(), $descriptor);

        return $descriptor;
    }
}
