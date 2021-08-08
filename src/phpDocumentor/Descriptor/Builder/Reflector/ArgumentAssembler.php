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

use phpDocumentor\Descriptor\ArgumentDescriptor;
use phpDocumentor\Descriptor\Builder\AssemblerAbstract as BaseAssembler;
use phpDocumentor\Descriptor\Tag\ParamDescriptor;
use phpDocumentor\Reflection\Php\Argument;

use function stripcslashes;

/**
 * Assembles an ArgumentDescriptor using an ArgumentReflector and ParamDescriptors.
 *
 * @extends BaseAssembler<ArgumentDescriptor, Argument>
 */
class ArgumentAssembler extends BaseAssembler
{
    /**
     * Creates a Descriptor from the provided data.
     *
     * @param Argument $data
     * @param iterable<ParamDescriptor> $params
     */
    public function create(object $data, iterable $params = []): ArgumentDescriptor
    {
        $argumentDescriptor = new ArgumentDescriptor();
        $argumentDescriptor->setName($data->getName());
        $argumentDescriptor->setType($data->getType());

        foreach ($params as $paramDescriptor) {
            $this->overwriteTypeAndDescriptionFromParamTag($data, $paramDescriptor, $argumentDescriptor);
        }

        $argumentDescriptor->setDefault($this->pretifyValue($data->getDefault()));
        $argumentDescriptor->setByReference($data->isByReference());
        $argumentDescriptor->setVariadic($data->isVariadic());

        return $argumentDescriptor;
    }

    /**
     * Overwrites the type and description in the Argument Descriptor with that from the tag if the names match.
     */
    protected function overwriteTypeAndDescriptionFromParamTag(
        Argument $argument,
        ParamDescriptor $paramDescriptor,
        ArgumentDescriptor $argumentDescriptor
    ): void {
        if ($paramDescriptor->getVariableName() !== $argument->getName()) {
            return;
        }

        $argumentDescriptor->setDescription($paramDescriptor->getDescription());
        $argumentDescriptor->setType($paramDescriptor->getType());
    }

    protected function pretifyValue(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return stripcslashes($value);
    }
}
