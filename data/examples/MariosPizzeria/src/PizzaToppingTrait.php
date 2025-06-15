<?php

namespace Marios;

/**
 * PizzaToppingTrait demonstrates a trait that uses another trait.
 *
 * This trait uses PizzaSauceTrait to showcase the bug in phpDocumentor
 * where traits used by other traits are not properly documented.
 */
trait PizzaToppingTrait
{
    use PizzaSauceTrait;

    protected array $toppings = ['Cheese', 'Mushroom', 'Pepperoni'];

    public function getToppings(): array
    {
        return $this->toppings;
    }

    public function preparePizza(string $size): string
    {
        // Using the applySauce method from PizzaSauceTrait
        $base = $this->applySauce($size);
        $toppingsStr = implode(', ', $this->toppings);

        return "{$base} and topped with {$toppingsStr}";
    }
}
