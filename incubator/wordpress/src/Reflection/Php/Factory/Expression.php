<?php

declare(strict_types=1);

namespace phpDocumentor\Wordpress\Reflection\Php\Factory;

use phpDocumentor\Reflection\Php\Factory;
use phpDocumentor\Reflection\Php\Factory\ContextStack;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;

final class Expression implements ProjectFactoryStrategy
{
    public function matches(ContextStack $context, object $object): bool
    {
        return $object instanceof \PhpParser\Node\Stmt\Expression;
    }

    public function create(ContextStack $context, object $object, StrategyContainer $strategies): void
    {
        $strategies->findMatching($context, $object->expr)->create($context, $object->expr, $strategies);
    }
}
