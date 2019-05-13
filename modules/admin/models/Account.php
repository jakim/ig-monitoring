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

    public $s_categories;

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_UPDATE] = [
            'name',
            'is_valid',
            'disabled',
        ];

        return $scenarios;
    }

    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            's_categories' => 'Categories',
            'is_valid' => 'Is Valid - an exclamation triangle in the list of accounts, is set automatically if the account is not reachable. Check this option if you are sure that this account is valid and want to try to refresh stats again.',
            'disabled' => 'Disabled - the account will disappear from the monitoring list and will be ignored even if it is automatically discovered.',
        ]);
    }

    public function getAccountStats()
    {
        return $this->hasMany(AccountStats::class, ['account_id' => 'id']);
    }
}