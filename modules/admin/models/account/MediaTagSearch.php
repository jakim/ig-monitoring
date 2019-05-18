<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 2018-09-26
 */

namespace app\modules\admin\models\account;


use app\components\stats\providers\AccountTagsDataProvider;
use app\components\visualizations\DateHelper;
use app\models\Account;
use yii\base\Model;

class MediaTagSearch extends Model
{
    public function search(Account $model)
    {
        $dateRange = DateHelper::getRangeFromUrl();
        list($from, $to) = DateHelper::normalizeRange($dateRange);

        $dataProvider = \Yii::createObject([
            'class' => AccountTagsDataProvider::class,
            'account' => $model,
            'from' => $from,
            'to' => $to,
        ]);

        return $dataProvider;
    }
}