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
use phpDocumentor\Descriptor\Builder\Reflector\AssemblerAbstract;
use phpDocumentor\Descriptor\Tag\MethodDescriptor;
use phpDocumentor\Descriptor\Tag\ReturnDescriptor;
use phpDocumentor\Reflection\DocBlock\Tags\Method;
use phpDocumentor\Reflection\Type;
use function array_key_exists;

/**
 * Constructs a new descriptor from the Reflector for an `@method` tag.
 *
 * This object will read the reflected information for the `@method` tag and create a {@see MethodDescriptor} object
 * that can be used in the rest of the application and templates.
 */
class MethodAssembler extends AssemblerAbstract
{
    /**
     * Creates a new Descriptor from the given Reflector.
     *
     * @param Method $data
     */
    public function create(object $data) : MethodDescriptor
    {
        $descriptor = new MethodDescriptor($data->getName());
        $descriptor->setDescription($data->getDescription());
        $descriptor->setMethodName($data->getMethodName());
        $descriptor->setStatic($data->isStatic());

        $response = new ReturnDescriptor('return');
        $response->setType($data->getReturnType());
        $descriptor->setResponse($response);

        /** @var array<string|Type> $argument */
        foreach ($data->getArguments() as $argument) {
            if (!array_key_exists('name', $argument) || !array_key_exists('type', $argument)) {
                continue;
            }

            $argumentDescriptor = $this->createArgumentDescriptorForMagicMethod(
                $argument['name'],
                $argument['type']
            );
            $descriptor->getArguments()->set($argumentDescriptor->getName(), $argumentDescriptor);
        }

        return $descriptor;
    }

    /**
     * Construct an argument descriptor given the array representing an argument with a Method Tag in the Reflection
     * component.
     */
    private function createArgumentDescriptorForMagicMethod(string $name, Type $type) : ArgumentDescriptor
    {
        $argumentDescriptor = new ArgumentDescriptor();
        $argumentDescriptor->setType(AssemblerAbstract::deduplicateTypes($type));
        $argumentDescriptor->setName($name);

        return $argumentDescriptor;
    }
}
