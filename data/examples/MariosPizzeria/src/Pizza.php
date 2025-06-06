<?php

declare(strict_types=1);

namespace Marios;

use ArrayObject;

/**
 * @package Domain
 */
final readonly class Pizza implements Product
{
    use SharedTrait { sayHello as private myPrivateHello; }

    public const TYPE_ITALIAN = 'italian';

    /**
     * Not a real pizza.
     *
     * Does not need much more of an explanation, does it? ;)
     *
     * @deprecated
     * @var string
     */
    final protected const TYPE_AMERICAN = 'american';

    private const TYPE_HYBRID = ['italian,spanish', 1, 'american'];

    /**
     * Name of your own custom Pizza.
     *
     * Want to show to your friends how cool your pizza baking skills are? Now you can! Name your Pizza anything you
     * want and stun them with your awesome creativity!
     */
    public readonly string $name = '';

    public static string $description = '';

    /**
     * I don't know what this does; can we delete this?
     * @deprecated
     * @var ArrayObject
     */
    protected $extra;

    /**
     * The best part of a Pizza is its secret ingredient.
     *
     * @var mixed Even the type of this is secret!
     */
    private $secretIngredient;

    /**
     * @var true
     */
    private bool $alwaysTrue = true;

    /**
     * Properties for NewTest.
     *
     * @var float $property1 OneProp
     * @var float $property2 TwoProp
     */
    private float $property1, $property2, $property3;

    public private(set) string $asymmetric;

    /**
     * This is a virtual property.
     *
     * phpDocumentor should display this as a read-only property, even though it is not defined as such.
     */
    public float $temperature {
        get { return $this->property1; }
    }

    /**
     * Returns the moisture of the pizza.
     *
     * This property is used to determine how moist the pizza is, which can affect the overall taste and texture.
     */
    public private(set) float $moisture {
        /**
         * value is calculated during the time in the oven.
         *
         * Description
         *
         * @return int<0, 10> pizza's moisture level
         */
        get { return $this->property2; }
        set(int|float $value) { $this->property2 = $value; }
    }

    /**
     * Number of toppings on the pizza.
     *
     * This is a non-virtual property with hooks. It uses the same name for backing field.
     */
    public int $toppingCount = 0 {
        get { return $this->toppingCount; }
        set(int $value) {
            if ($value < 0) {
                throw new \InvalidArgumentException("Cannot have negative toppings");
            }
            $this->toppingCount = $value;
        }
    }

    /**
     * Size of the pizza (small, medium, large).
     *
     * This is a non-virtual property with hooks and custom validation.
     */
    public string $size = 'medium' {
        get { return $this->size; }
        set(string $value) {
            $value = strtolower($value);

            if (!in_array($value, ['small', 'medium', 'large'])) {
                throw new \InvalidArgumentException("Size must be small, medium, or large");
            }
            $this->size = $value;
        }
    }

    /**
     * Price of the pizza.
     *
     * This is a non-virtual property with hooks and formatting.
     */
    public float $price = 0.0 {
        get {
            return $this->price * 1.09;
        }
        set(float $value) {
            if ($value < 0) {
                throw new \InvalidArgumentException("Price cannot be negative");
            }
            $this->price = $value;
        }
    }

    /**
     * The ingredients array for the pizza.
     *
     * This demonstrates property hooks with arrays.
     *
     * @var string[]
     */
    public array $ingredients {
        get { return ['cheese', 'tomato', 'dough']; }
        set(array $value) {}
    }

    /**
     * Reference to the pizzeria this pizza belongs to.
     *
     * Demonstrates property hooks with references.
     */
    public object $pizzeria {
        get { return $GLOBALS['pizzeria'] ?? new \stdClass(); }
        set(&$value) { $GLOBALS['pizzeria'] = $value; }
    }

    /**
     * Property with default value.
     *
     * This property has a default value when accessed for the first time.
     */
    public int $cookingTime {
        get { return $this->property3 ?? 15; }
        set(int $value) { $this->property3 = $value; }
    }

    /**
     * Special instructions for preparing the pizza.
     *
     * This is a write-only property that can only be set, not read.
     * It demonstrates the write-only property hook pattern.
     */
    public string $instructions {
        set(string $value) {
            error_log("Pizza instructions received: " . $value);
        }
    }

    /**
     * Demonstrates hook inheritance from a parent class.
     */
    public string $base {
        get { return "Thin crust"; }
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Om nom nom.
     *
     * What else do you do with a pizza? Put it in your freezer?
     *
     * @return void
     */
    #[Route('/very/cool/route/{foo}/{bar}', name: 'very_cool_route',
        defaults: ['foo' => 'foo', 'bar' => 'bar'])]
    public function eatIt()
    {
    }
}
