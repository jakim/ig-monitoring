<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 09.05.2018
 */

namespace app\components\http;


use app\models\Proxy;
use Carbon\Carbon;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\db\Expression;

class ProxyManager extends Component
{
    /**
     * Between requests, in seconds.
     *
     * @var int
     */
    public $restTime = 1;

    /**
     * After IG block, in hours.
     *
     * @var int
     */
    public $blockRestTime = 1;

    public function reserve(bool $ignoreRest = false): Proxy
    {
        $uid = $this->generateUid();
        $condition = [
            'and',
            ['active' => 1],
            ['reservation_uid' => null],
            ['<=', 'updated_at', new Expression(sprintf('DATE_ADD(NOW(), INTERVAL %s SECOND)', (int)$this->restTime))],
        ];

        $columns = [
            'reservation_uid' => $uid,
            'updated_at' => Carbon::now()->toDateTimeString(),
        ];

        if (!$ignoreRest) {
            $condition[] = ['or',
                ['rest_until' => null],
                new Expression('rest_until<=now()'),
            ];
            // btw, if rest is done, can be reset
            $columns['rests'] = 0;
            $columns['rest_until'] = null;
        }

        $sql = Yii::$app->db->createCommand()
            ->update('proxy', $columns, $condition)
            ->rawSql;

        $n = Yii::$app->db->createCommand("{$sql} ORDER BY [[updated_at]] ASC LIMIT 1")
            ->execute();

        if (empty($n)) {
            throw new NoProxyAvailableException();
        }

        $proxy = Proxy::findOne(['reservation_uid' => $uid]);

        if (empty($proxy)) {
            throw new InvalidConfigException('Something is wrong with \'ProxyManager\'.');
        }

        return $proxy;
    }

    public function release(Proxy $proxy, ?bool $rest = null)
    {
        $attributes = ['reservation_uid' => null];
        if ($rest === true) {
            Yii::debug(sprintf("REST: %s\n", "{$proxy->ip}:{$proxy->port}"), __METHOD__);
            $attributes['rests'] = new Expression('rests+1');
            $attributes['rest_until'] = new Expression(sprintf('DATE_ADD(NOW(), INTERVAL rests+%s HOUR)', (int)$this->blockRestTime));
        } elseif ($rest === false) {
            $attributes['rests'] = 0;
            $attributes['rest_until'] = null;
        }

        Proxy::updateAll($attributes, ['id' => $proxy->id]);
    }

    protected function generateUid()
    {
        return sprintf('%s_%s', Yii::$app->security->generateRandomString(64), time());
    }
}