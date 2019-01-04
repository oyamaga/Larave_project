<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Address::class, function (Faker $faker) {
    return [
        'address' => $faker->address,
        'email' => $faker->email,
    ];
});
