<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

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
