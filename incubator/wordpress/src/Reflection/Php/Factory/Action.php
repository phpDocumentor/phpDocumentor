<?php

declare(strict_types=1);

namespace phpDocumentor\Wordpress\Reflection\Php\Factory;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use phpDocumentor\Reflection\Metadata\MetaDataContainer;
use phpDocumentor\Reflection\Php\Factory\AbstractFactory;
use phpDocumentor\Reflection\Php\Factory\ContextStack;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Wordpress\Reflection\Php\Action as ActionElement;
use phpDocumentor\Wordpress\Reflection\Php\Hooks;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\PrettyPrinter\Standard as PrettyPrinter;

final class Action extends AbstractFactory
{
    private const ACTION_NAMES = [
        'do_action',
        'do_action_ref_array',
        'do_action_deprecated',
    ];
    /**
     * @var PrettyPrinter
     */
    private $valuePrinter;

    public function __construct(DocBlockFactoryInterface $docBlockFactory, PrettyPrinter $valuePrinter)
    {
        parent::__construct($docBlockFactory);
        $this->valuePrinter = $valuePrinter;
    }


    public function matches(ContextStack $context, object $object): bool
    {
        return $object instanceof FuncCall && $context->peek() instanceof MetaDataContainer && in_array($object->name->toString(), self::ACTION_NAMES);
    }

    public function doCreate(ContextStack $context, object $object, StrategyContainer $strategies): void
    {
        /** @var MetaDataContainer $container */
        $container = $context->peek();

        $hooks = new Hooks();
        $existing = $container->getMetadata()[$hooks->key()] ?? $hooks;
        if ($existing === $hooks) {
            $container->addMetadata($hooks);
        }

        $existing->addAction(
            new ActionElement(
                $this->valuePrinter->prettyPrintExpr($object->args[0]->value),
                $this->createDocBlock($object->getDocComment(), $context->getTypeContext()) ?? new DocBlock()
            )
        );
    }
}
