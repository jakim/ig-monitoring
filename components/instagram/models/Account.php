<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 11.06.2018
 */

namespace app\components\instagram\models;


class Account
{
    public $id;
    public $username;
    public $profilePicUrl;
    public $fullName;
    public $biography;
    public $externalUrl;
    public $followedBy;
    public $follows;
    public $media;
    public $isPrivate;
}