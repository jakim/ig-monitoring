<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 11.01.2018
 */

namespace app\components;


use app\jobs\UpdateAccountJob;
use app\jobs\UpdateTagJob;
use app\models\Account;
use app\models\Tag;

class JobFactory
{

    public static function updateAccount($accountId): UpdateAccountJob
    {
        $job = new UpdateAccountJob();
        $job->id = $accountId instanceof Account ? $accountId->id : $accountId;

        return $job;
    }

    public static function updateTag($tagId): UpdateTagJob
    {
        $job = new UpdateTagJob();
        $job->id = $tagId instanceof Tag ? $tagId->id : $tagId;

        return $job;
    }
}