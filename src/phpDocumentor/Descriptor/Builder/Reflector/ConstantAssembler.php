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

use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\Interfaces\ConstantInterface;
use phpDocumentor\Reflection\Php\Constant;

use function str_contains;
use function strlen;
use function substr;

/**
 * Assembles a ConstantDescriptor from a ConstantReflector.
 *
 * @extends AssemblerAbstract<ConstantInterface, Constant>
 */
class ConstantAssembler extends AssemblerAbstract
{
    final public const SEPARATOR_SIZE = 2;

    /**
     * Creates a Descriptor from the provided data.
     *
     * @param Constant $data
     */
    public function buildDescriptor(object $data): ConstantInterface
    {
        $constantDescriptor = new ConstantDescriptor();
        $constantDescriptor->setName($data->getName());
        $constantDescriptor->setValue($data->getValue(false));
        $constantDescriptor->setFinal($data->isFinal());
        // Reflection library formulates namespace as global but this is not wanted for phpDocumentor itself

        $separatorLength = ! str_contains((string) $data->getFqsen(), '::') ? 1 : 2;
        $constantDescriptor->setNamespace(
            substr((string) $data->getFqsen(), 0, -strlen($data->getName()) - $separatorLength),
        );
        $constantDescriptor->setFullyQualifiedStructuralElementName($data->getFqsen());

        $this->assembleDocBlock($data->getDocBlock(), $constantDescriptor);

        $constantDescriptor->setStartLocation($data->getLocation());
        $constantDescriptor->setEndLocation($data->getEndLocation());

        return $constantDescriptor;
    }
}
