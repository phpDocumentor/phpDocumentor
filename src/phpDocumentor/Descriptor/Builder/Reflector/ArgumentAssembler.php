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
use phpDocumentor\Descriptor\Interfaces\ArgumentInterface;
use phpDocumentor\Descriptor\Tag\ParamDescriptor;
use phpDocumentor\Descriptor\ValueObjects\IsApplicable;
use phpDocumentor\Reflection\Php\Argument;

/**
 * Assembles an ArgumentDescriptor using an ArgumentReflector and ParamDescriptors.
 *
 * @extends BaseAssembler<ArgumentInterface, Argument>
 */
class ArgumentAssembler extends BaseAssembler
{
    /**
     * Creates a Descriptor from the provided data.
     *
     * @param Argument $data
     * @param iterable<ParamDescriptor> $params
     */
    public function create(object $data, iterable $params = []): ArgumentInterface
    {
        $argumentDescriptor = new ArgumentDescriptor();
        $argumentDescriptor->setName($data->getName());
        $argumentDescriptor->setType($data->getType());

        foreach ($params as $paramDescriptor) {
            $this->overwriteTypeAndDescriptionFromParamTag($data, $paramDescriptor, $argumentDescriptor);
        }

        $argumentDescriptor->setDefault($data->getDefault(false));
        $argumentDescriptor->setByReference(IsApplicable::fromBoolean($data->isByReference()));
        $argumentDescriptor->setVariadic(IsApplicable::fromBoolean($data->isVariadic()));

        return $argumentDescriptor;
    }

    /**
     * Overwrites the type and description in the Argument Descriptor with that from the tag if the names match.
     */
    protected function overwriteTypeAndDescriptionFromParamTag(
        Argument $argument,
        ParamDescriptor $paramDescriptor,
        ArgumentInterface $argumentDescriptor,
    ): void {
        if ($paramDescriptor->getVariableName() !== $argument->getName()) {
            return;
        }

        $argumentDescriptor->setDescription($paramDescriptor->getDescription());
        $argumentDescriptor->setType($paramDescriptor->getType());
    }
}
