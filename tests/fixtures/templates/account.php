<?php

/**
 * @var \Faker\Generator $faker
 * @var integer $index
 */

return [
    'username' => $faker->userName . $index,
    'monitoring' => 1,
    'proxy_id' => null,
    'proxy_tag_id' => null,
    'name' => $faker->colorName . $index,
//    'profile_pic_url' => null,
    'full_name' => $faker->name,
    'biography' => substr($faker->paragraph, 0, 255),
    'external_url' => $faker->url,
    'instagram_id' => $faker->randomDigitNotNull . $index,
    'uid' => $faker->uuid . $index,
//    'disabled' => 0,
];