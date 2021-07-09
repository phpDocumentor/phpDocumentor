<?php

declare(strict_types=1);

namespace Marios;

use ArrayObject;
use JetBrains\PhpStorm\Pure;

/**
 * @package Domain
 */
final class Pizza implements Product
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
    protected const TYPE_AMERICAN = 'american';

    private const TYPE_HYBRID = ['italian,spanish', 1, 'american'];

    /**
     * Name of your own custom Pizza.
     *
     * Want to show to your friends how cool your pizza baking skills are? Now you can! Name your Pizza anything you
     * want and stun them with your awesome creativity!
     */
    public string $name = '';

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

    private float $property1, $property2, $property3;

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
