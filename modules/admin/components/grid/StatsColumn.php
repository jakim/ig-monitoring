<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 19.01.2018
 */

namespace app\modules\admin\components\grid;


use app\models\Account;
use app\modules\admin\components\AccountStatsManager;
use yii\di\Instance;
use yii\grid\DataColumn;

class StatsColumn extends DataColumn
{
    public $format = 'html';
    public $numberFormat = 'integer';
    public $statsAttribute;
    public $headerOptions = ['class' => 'sort-numerical'];

    public $statsManager = AccountStatsManager::class;

    public function init()
    {
        parent::init();
        if (!$this->statsAttribute) {
            $this->statsAttribute = $this->attribute;
        }
    }

    /**
     * @param \app\modules\admin\models\Account|\app\modules\admin\models\Tag $model
     * @param mixed $key
     * @param int $index
     * @return null|string
     * @throws \yii\base\InvalidConfigException
     */
    public function getDataCellValue($model, $key, $index)
    {
        if (!$model->lastStats) {
            return null;
        }

        /** @var \app\components\Formatter $formatter */
        $formatter = $this->grid->formatter;

        if ($model instanceof Account) {
            /** @var \app\modules\admin\components\AccountStatsManager $manager */
            $manager = \Yii::createObject([
                'class' => $this->statsManager,
                'account' => $model,
            ]);
            $lastChange = $manager->lastChange($this->statsAttribute);
            $monthlyChange = $manager->lastMonthChange($this->statsAttribute);
        } else {
            $lastChange = $model->lastChange($this->statsAttribute);
            $monthlyChange = $model->monthlyChange($this->statsAttribute);
        }

        return sprintf(
            "%s (%s/%s)",
            $formatter->format($model->lastStats->{$this->statsAttribute}, $this->numberFormat),
            $lastChange ? $formatter->asChange($lastChange, true, $this->numberFormat) : 0,
            $monthlyChange ? $formatter->asChange($monthlyChange, true, $this->numberFormat) : 0
        );
    }
}