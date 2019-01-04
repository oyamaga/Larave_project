<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Item::class, function (Faker $faker) {
    return [
        'item_name' => 'テストアイテム'.$faker->randomNumber(2),
    ];
});
