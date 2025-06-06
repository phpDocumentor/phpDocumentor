<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Builder\Reflector;

use InvalidArgumentException;
use phpDocumentor\Descriptor\Builder\AssemblerReducer;
use phpDocumentor\Descriptor\Interfaces\PropertyHookInterface;
use phpDocumentor\Descriptor\PropertyHookDescriptor;
use phpDocumentor\Descriptor\ValueObjects\Visibility;
use phpDocumentor\Descriptor\ValueObjects\VisibilityModifier;
use phpDocumentor\Reflection\Php\AsymmetricVisibility;
use phpDocumentor\Reflection\Php\PropertyHook;

/**
 * Assembles a PropertyDescriptor from a PropertyReflector.
 *
 * @extends AssemblerAbstract<PropertyHookInterface, PropertyHook>
 */
final class PropertyHookAssembler extends AssemblerAbstract
{
    public function __construct(
        private ArgumentAssembler $argumentAssembler,
        AssemblerReducer ...$reducers,
    ) {
        parent::__construct(...$reducers);
    }

    protected function buildDescriptor(object $data): PropertyHookInterface
    {
        $propertyHook = new PropertyHookDescriptor();
        $propertyHook->setName($data->getName());

        $propertyHook->setVisibility($this->buildVisibility($data));
        $this->assembleDocBlock($data->getDocBlock(), $propertyHook);

        foreach ($data->getArguments() as $argument) {
            $propertyHook->addArgument(
                $argument->getName(),
                $this->argumentAssembler->create($argument, []),
            );
        }

        return $propertyHook;
    }

    private function buildVisibility(PropertyHook $data): Visibility
    {
        if ($data->getVisibility() instanceof AsymmetricVisibility) {
            return match ($data->getName()) {
                'get' => new Visibility(
                    VisibilityModifier::from((string) $data->getVisibility()->getReadVisibility()),
                ),
                'set' =>  new Visibility(
                    VisibilityModifier::from((string) $data->getVisibility()->getWriteVisibility()),
                ),
                default => throw new InvalidArgumentException(
                    'Asymmetric visibility is only supported for "get" and "set" hooks.',
                ),
            };
        }

        if ($data->getVisibility() === null) {
            return new Visibility(VisibilityModifier::PUBLIC);
        }

        return new Visibility(
            VisibilityModifier::from((string) $data->getVisibility()),
        );
    }
}
