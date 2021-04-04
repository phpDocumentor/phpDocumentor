<?php declare(strict_types=1);

namespace Marios;

/**
 * Trait that all pizza's could share.
 *
 * Okay, so I couldn't think of something that fits the theme .. If you have a cool idea: please issue a PR :)
 */
trait SharedTrait
{
    public Pizza\Base $base;

    protected string $sharedProperty;

    private bool $secretProperty;

    public function sayHello(): Pizza\Base {

    }
}
