<?php

use Faker\Generator as Faker;

$factory->define(App\Admin\Models\TestUser::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
    ];
});
