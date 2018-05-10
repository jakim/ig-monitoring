<?php

/**
 * @var \Faker\Generator $faker
 * @var integer $index
 */

return [
    'is_video' => rand(0, 1),
    'monitoring' => rand(0, 1),
    'shortcode' => $faker->safeColorName . $index,
    'caption' => substr($faker->paragraph, 0, 255),
    'taken_at' => $faker->date('Y-m-d H:i:s'),
    'instagram_id' => $faker->safeColorName . $index,
];