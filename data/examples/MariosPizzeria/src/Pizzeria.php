<?php

declare(strict_types=1);

namespace Marios;

/**
 * Entrypoint for this pizza ordering application.
 *
 * This class provides an interface through which you can order pizza's and pasta's from Mario's Pizzeria.
 *
 * We have:
 * - American pizzas
 * - And real (italian) pizzas
 *
 * @link https://wwww.phpdoc.org
 * @link https://docs.phpdoc.org docs
 *
 * @since 3.0
 * @since 3.1 Does extra stuff
 */
final class Pizzeria implements \JsonSerializable
{
    public function order(Pizza ...$pizzas): bool
    {
        return true;
    }

    /**
     * Places an order for a pizza.
     *
     * This is an example of a protected function with the static modifier whose parameters' type and return type is
     * determined by the DocBlock and no type hints are given in the method signature.
     *
     * @param Pizza $pizza The specific pizza to place an order for.
     *
     * @return bool Whether the order succeeded
     */
    protected static function doOrder($pizza)
    {
        return true;
    }

    /**
     * @deprecated This ordering method should no longer be used; it will always fail.
     *
     * @return false Demonstrate that 'false' is a valid return type in an DocBlock to indicate it won't just return any
     *      boolean; it will _always_ be false.
     */
    private final function doOldOrder(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return [];
    }
}
