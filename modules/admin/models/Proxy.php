<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 10.03.2018
 */

namespace app\modules\admin\models;


use app\components\ArrayHelper;

class Proxy extends \app\models\Proxy
{
    public $tagString;

    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['tagString'], 'safe'],
        ]);
    }

    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'tagString' => 'Tags',
        ]);
    }
}