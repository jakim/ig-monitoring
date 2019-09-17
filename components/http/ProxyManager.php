<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 09.05.2018
 */

namespace app\components\http;


use app\models\Proxy;
use DateTime;
use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\base\InvalidConfigException;

class ProxyManager extends Component
{
    public $restTime = 1;

    public function reserve($model): Proxy
    {
        $uid = $this->generateUid();

        $condition = $this->prepareCondition($model);

        $sql = Yii::$app->db->createCommand()
            ->update('proxy', [
                'reservation_uid' => $uid,
                'updated_at' => (new DateTime())->format('Y-m-d H:i:s'),
            ], $condition)->rawSql;

        $n = Yii::$app->db->createCommand("{$sql} ORDER BY [[updated_at]] ASC LIMIT 1")
            ->execute();

        if (empty($n)) {
            throw new Exception('No proxy available.');
        }

        $proxy = Proxy::findOne(['reservation_uid' => $uid]);

        if (empty($proxy)) {
            throw new InvalidConfigException('Something is wrong with \'ProxyManager\'.');
        }

        return $proxy;
    }

    public function release(Proxy $proxy)
    {
        Proxy::updateAll(['reservation_uid' => null], 'reservation_uid=:reservation_uid', [':reservation_uid' => $proxy->reservation_uid]);
    }

    public function invalidate(Proxy $proxy)
    {
        Proxy::updateAll(['active' => false], 'id=:id', [':id' => $proxy->id]);
    }

    protected function generateUid()
    {
        return sprintf('%s_%s', Yii::$app->security->generateRandomString(64), time());
    }

    private function prepareCondition($model)
    {
        $andWhere = [
            'and',
            ['active' => 1],
            ['reservation_uid' => null],
            ['<=', 'updated_at', (new DateTime(sprintf('-%d seconds', (int) $this->restTime)))->format('Y-m-d H:i:s')],
        ];

        if ($model->proxy_id) {
            $andWhere[] = ['id' => $model->proxy_id];
        }

        return $andWhere;
    }
}