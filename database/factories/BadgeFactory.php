<?php

use Faker\Generator as Faker;

$factory->define(Badge::class, function (Faker $faker) {
    return [
        'id' => $faker->randomDigitNotNull
    ];
});
