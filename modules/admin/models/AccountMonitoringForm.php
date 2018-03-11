<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 11.03.2018
 */

namespace app\modules\admin\models;


use yii\base\Model;

class AccountMonitoringForm extends Model
{
    public $usernames;
    public $tags;
    public $proxy_id;
    public $proxy_tag_id;

    public function rules()
    {
        return [
            ['usernames', 'required'],
            [['usernames', 'tags', 'proxy_id', 'proxy_tag_id'], 'safe'],
        ];
    }
}