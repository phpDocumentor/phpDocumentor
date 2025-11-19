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

namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use phpDocumentor\Descriptor\ArgumentDescriptor;
use phpDocumentor\Descriptor\Tag\MethodDescriptor;
use phpDocumentor\Descriptor\Tag\ReturnDescriptor;
use phpDocumentor\Descriptor\ValueObjects\IsApplicable;
use phpDocumentor\Reflection\DocBlock\Tags\Method;
use phpDocumentor\Reflection\Php\Expression;

/**
 * Constructs a new descriptor from the Reflector for an `@method` tag.
 *
 * This object will read the reflected information for the `@method` tag and create a {@see MethodDescriptor} object
 * that can be used in the rest of the application and templates.
 *
 * @extends BaseTagAssembler<MethodDescriptor, Method>
 */
class MethodAssembler extends BaseTagAssembler
{
    /**
     * Creates a new Descriptor from the given Reflector.
     *
     * @param Method $data
     */
    public function buildDescriptor(object $data): MethodDescriptor
    {
        $descriptor = new MethodDescriptor($data->getName());
        $descriptor->setMethodName($data->getMethodName());
        $descriptor->setStatic($data->isStatic());
        $descriptor->setHasReturnByReference($data->returnsReference());

        $response = new ReturnDescriptor('return');
        $response->setType($data->getReturnType());
        $descriptor->setResponse($response);

        foreach ($data->getParameters() as $argument) {
            $argumentDescriptor = new ArgumentDescriptor();
            $argumentDescriptor->setName($argument->getName());
            $argumentDescriptor->setType($argument->getType());
            $argumentDescriptor->setVariadic(IsApplicable::fromBoolean($argument->isVariadic()));
            $argumentDescriptor->setByReference(IsApplicable::fromBoolean($argument->isReference()));
            if ($argument->getDefaultValue() !== null) {
                $argumentDescriptor->setDefault(new Expression($argument->getDefaultValue()));
            }

            $descriptor->getArguments()->set($argumentDescriptor->getName(), $argumentDescriptor);
        }

        return $descriptor;
    }
}
