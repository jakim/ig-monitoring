<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 11.03.2018
 */

namespace app\modules\admin\models;


use app\dictionaries\TrackerType;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class MonitoringForm extends Model
{
    public $names;
    public $tags;
    public $proxy_id;

    public function rules()
    {
        return [
            ['names', 'required'],
            [['names', 'tags', 'proxy_id'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return [
            TrackerType::ACCOUNT => [
                'names',
                'tags', 'proxy_id'
            ],
            TrackerType::TAG => [
                'names',
                'proxy_id'
            ],
        ];
    }

    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $trackerTypeLabels = [
            TrackerType::ACCOUNT => [
                'names' => 'Accounts',
            ],
            TrackerType::TAG => [
                'names' => 'Tags',
            ],
        ];

        return ArrayHelper::merge($labels, ArrayHelper::getValue($trackerTypeLabels, $this->scenario, []));
    }

    public function attributeHints()
    {
        $hints = [
            'names' => 'Comma separated list.',
        ];
        $trackerTypeHints = [
            TrackerType::ACCOUNT => [],
            TrackerType::TAG => [],
        ];

        return ArrayHelper::merge($hints, ArrayHelper::getValue($trackerTypeHints, $this->scenario, []));
    }
}