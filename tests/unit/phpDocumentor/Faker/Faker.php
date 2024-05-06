<?php

declare(strict_types=1);

namespace phpDocumentor\Faker;

use Faker\Factory;
use Faker\Generator;

trait Faker
{
    /** @var Generator */
    private static $faker = null;

    public static function faker(): Generator
    {
        if (self::$faker === null) {
            self::$faker = Factory::create();
            self::$faker->addProvider(new Provider(self::$faker));
        }

        return self::$faker;
    }
}
