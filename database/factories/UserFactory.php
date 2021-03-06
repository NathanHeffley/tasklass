<?php

use Faker\Generator as Faker;

$factory->define(App\User::class, function (Faker $faker) {
    static $password;

    return [
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
        'confirmed' => false,
    ];
});

$factory->state(App\User::class, 'confirmed', function (Faker $faker) {
    return [
        'confirmed' => true,
    ];
});
