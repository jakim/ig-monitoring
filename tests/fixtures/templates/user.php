<?php

/**
 * @var \Faker\Generator $faker
 * @var integer $index
 */

return [
    'username' => $faker->userName . $index,
    'email' => $faker->email,
    'google_user_id' => "gui_{$index}",
    'active' => 1,
    'access_token' => "access_token_{$index}",
];