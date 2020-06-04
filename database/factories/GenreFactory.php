<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Genre;
use Faker\Generator as Faker;

$factory->define(Genre::class, function (Faker $faker) {
    return [
        'name' => $faker->monthName,
        'is_active' => $faker->boolean(65)
    ];
});
