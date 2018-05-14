<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 14.05.2018
 */

namespace app\modules\api\v1\models;


use yii\base\Model;

class AccountSearchForm extends Model
{
    public $username;

    public function rules()
    {
        return [
            [['username'], 'safe'],
        ];
    }
}