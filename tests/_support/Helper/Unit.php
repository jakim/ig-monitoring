<?php

namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Unit extends \Codeception\Module
{
    /**
     * @param int $modifier
     * @return \Jakim\Model\Post
     */
    public function postDummyData(int $modifier = 1)
    {
        $data = new \Jakim\Model\Post();
        $data->id = "test{$modifier}";
        $data->shortcode = "test{$modifier}";
        $data->url = "test{$modifier}";
        $data->isVideo = false;
        $data->caption = "test{$modifier}";
        $data->likes = $modifier;
        $data->comments = $modifier;
        $data->takenAt = time();

        return $data;
    }

    /**
     * @param int $modifier
     * @return \Jakim\Model\Account
     */
    public function accountDummyData(int $modifier = 1): \Jakim\Model\Account
    {
        $data = new \Jakim\Model\Account();
        $data->username = "test_data{$modifier}";
        $data->fullName = "test_data{$modifier}";
        $data->biography = "test_data{$modifier}";
        $data->externalUrl = "test_data{$modifier}";
        $data->id = "test_data{$modifier}";
        $data->profilePicUrl = 'test_profile_pic.jpg';
        $data->media = $modifier;
        $data->follows = $modifier;
        $data->followedBy = $modifier;

        return $data;
    }
}
