<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 11.01.2018
 */

namespace app\components;


use app\jobs\AccountUpdate;
use app\jobs\TagUpdate;
use app\models\Account;
use app\models\Tag;

class JobFactory
{

    public static function createAccountUpdate($accountId): AccountUpdate
    {
        $job = new AccountUpdate();
        $job->id = $accountId instanceof Account ? $accountId->id : $accountId;

        return $job;
    }

    public static function createTagUpdate($tagId): TagUpdate
    {
        $job = new TagUpdate();
        $job->id = $tagId instanceof Tag ? $tagId->id : $tagId;

        return $job;
    }
}