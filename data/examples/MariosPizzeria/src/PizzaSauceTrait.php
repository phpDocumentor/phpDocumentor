<?php
declare(strict_types=1);

namespace Marios;
/**
 * PizzaSauceTrait provides basic saucing functionality for pizzas.
 *
 * This trait is used to demonstrate the bug where phpDocumentor doesn't document
 * traits that are used by other traits.
 */
trait PizzaSauceTrait
{
    /**
     * Version of the sauce recipe
     */
    public const SAUCE_VERSION = '1.0';

    protected string $sauceType = 'Tomato';

    public function getSauceType(): string
    {
        return $this->sauceType;
    }

    protected function applySauce(string $sauceAmount): string
    {
        return "Applied {$sauceAmount} of {$this->sauceType} sauce to the base";
    }
}
