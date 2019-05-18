<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 2018-09-27
 */

namespace app\modules\admin\models\account;


use app\components\stats\providers\AccountAccountsDataProvider;
use app\components\visualizations\DateHelper;
use app\models\Account;
use Yii;

class MediaAccountSearch extends StatsSearch
{
    public function search(Account $model, array $params = [])
    {
        $dateRange = DateHelper::getRangeFromUrl();
        list($from, $to) = DateHelper::normalizeRange($dateRange);

        $dataProvider = Yii::createObject([
            'class' => AccountAccountsDataProvider::class,
            'account' => $model,
            'from' => $from,
            'to' => $to,
        ]);


        return $dataProvider;
    }

}