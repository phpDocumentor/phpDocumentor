<?php

declare(strict_types=1);

namespace Marios;

/**
 * @package Domain
 */
final class Pizza
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
    private const TYPE_AMERICAN = 'american';

    private const TYPE_HYBRID = ['italian,spanish',1, 'american'];

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
     */
    public $extra;

    /**
     * Om nom nom.
     *
     * What else do you do with a pizza? Put it in your freezer?
     *
     * @return void
     */
    public function eatIt()
    {
    }
}
