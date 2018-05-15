<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 15.05.2018
 */

namespace app\modules\api\v1\models;


use yii\base\Model;

class DataFilterForm extends Model
{
    public $er;
    public $followed_by;
    public $follows;
    public $media;
    public $username;

    public function rules()
    {
        return [
            [['er', 'followed_by', 'follows', 'media', 'username'], 'safe'],
        ];
    }
}