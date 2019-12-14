<?php

declare(strict_types=1);

namespace phpDocumentor\Faker;

use Faker\Factory;
use Faker\Generator;

trait Faker
{
    /** @var Generator */
    private $faker;

    /**
     * @return Provider|Generator
     */
    public function faker() : Generator
    {
        if ($this->faker === null) {
            $this->faker = Factory::create();
            $this->faker->addProvider(new Provider($this->faker));
        }

        return $this->faker;
    }
}
