<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 27.03.2018
 */

namespace app\modules\admin\models;


use yii\base\Model;

class TagMonitoringForm extends Model
{
    public $names;
    public $proxy_id;
    public $proxy_tag_id;

    public function rules()
    {
        return [
            ['names', 'required'],
            [['names', 'proxy_id', 'proxy_tag_id'], 'safe'],
        ];
    }
}