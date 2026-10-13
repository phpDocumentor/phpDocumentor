<?php

declare(strict_types=1);

namespace phpDocumentor\WordpressHooks\Reflection\Php\Factory;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use phpDocumentor\Reflection\Metadata\MetaDataContainer;
use phpDocumentor\Reflection\Php\Factory\AbstractFactory;
use phpDocumentor\Reflection\Php\Factory\ContextStack;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\WordpressHooks\Reflection\Php\Action;
use phpDocumentor\WordpressHooks\Reflection\Php\Hooks;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\PrettyPrinter\Standard as PrettyPrinter;

use function in_array;

final class ActionFactory extends AbstractFactory
{
    private const ACTION_NAMES = ['do_action', 'do_action_ref_array', 'do_action_deprecated'];

    public function __construct(DocBlockFactoryInterface $docBlockFactory, private readonly PrettyPrinter $printer)
    {
        parent::__construct($docBlockFactory);
    }

    public function matches(ContextStack $context, object $object): bool
    {
        return $object instanceof FuncCall
            && $context->peek() instanceof MetaDataContainer
            && in_array($object->name->toString(), self::ACTION_NAMES, true);
    }

    protected function doCreate(ContextStack $context, object $object, StrategyContainer $strategies): object|null
    {
        $container = $context->peek();

        $hooks = $container->getMetadata()['wp_hooks'] ?? null;
        if ($hooks === null) {
            $hooks = new Hooks();
            $container->addMetadata($hooks);
        }

        $action = new Action(
            $this->printer->prettyPrintExpr($object->args[0]->value),
            $this->createDocBlock($object->getDocComment(), $context->getTypeContext()) ?? new DocBlock(),
        );
        $hooks->addAction($action);

        return $action;
    }
}
