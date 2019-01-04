<?php

use Faker\Generator as Faker;

$factory->define(App\Models\UserItemRelation::class, function (Faker $faker) {
    return [
        'quantity' => $faker->randomDigit,
    ];
});
