<?php

namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Api extends \Codeception\Module
{
    public static function responseAccountJsonType()
    {
        return [
            'id' => 'integer',
            'uid' => 'string|null',
            'username' => 'string',
            'monitoring' => 'boolean|null',
            'disabled' => 'boolean|null',
            'name' => 'string|null',
            'profile_pic_url' => 'string:url|null',
            'full_name' => 'string|null',
            'biography' => 'string|null',
            'external_url' => 'string|null',
            'instagram_id' => 'string|null',
            'updated_at' => 'string|null',
            'created_at' => 'string|null',
        ];
    }
}
