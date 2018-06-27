<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 23.01.2018
 */

namespace app\modules\admin\models;


use app\components\ArrayHelper;

class Account extends \app\models\Account
{
    const SCENARIO_UPDATE = 'update';

    public $as_followed_by;
    public $as_follows;
    public $as_media;
    public $as_er;
    public $as_created_at;

    public $s_tags;

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_UPDATE] = [
            'name',
            'accounts_monitoring_level',
            'disabled',
            'accounts_default_tags',
        ];

        return $scenarios;
    }

    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'as_followed_by' => 'Followed By',
            'as_follows' => 'Follows',
            'as_media' => 'Media',
            'as_er' => 'Engagement',
            's_tags' => 'Tags',
            'as_created_at' => 'Created At',
            'disabled' => 'An exclamation triangle in the list of accounts, is set automatically if the account is not reachable. Disabled this option if you are sure that this account exists and want to try to refresh stats again.',
        ]);
    }

    public function getAccountStats()
    {
        return $this->hasMany(AccountStats::class, ['account_id' => 'id']);
    }
}