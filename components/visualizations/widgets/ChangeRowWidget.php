<?php
/**
 * Created for monitoring-free.
 * User: jakim <pawel@jakimowski.info>
 * Date: 2019-05-14
 */

namespace app\components\visualizations\widgets;


use app\components\stats\traits\StatsAttributesTrait;
use app\components\traits\SetAccountTrait;
use app\modules\admin\widgets\ChangeInfoBox;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class ChangeRowWidget extends Widget
{
    use SetAccountTrait, StatsAttributesTrait;

    public $header;

    public $cellCssClass = 'col-lg-3';

    /**
     * Config \app\components\stats\diffs\AccountDiff
     *
     * @var array|\app\components\stats\diffs\AccountDiff
     */
    public $diff;

    /**
     * @var \app\components\Formatter
     */
    protected $formatter;

    public function init()
    {
        parent::init();
        $this->diff = \Yii::createObject(ArrayHelper::merge($this->diff, [
            'account' => $this->account,
            'statsAttributes' => array_keys($this->statsAttributes),
        ]));
        $this->formatter = \Yii::$app->formatter;
    }

    public function run()
    {
        $this->renderHeader();
        $this->renderRow();
    }

    protected function renderCell($attribute, $format = 'integer')
    {
        echo "<div class=\"{$this->cellCssClass}\">";
        echo ChangeInfoBox::widget([
            'header' => $this->account->getAttributeLabel($attribute),
            'number' => ArrayHelper::getValue($this->diff->getData(), $attribute),
            'format' => $format,
        ]);
        echo '</div>';
    }

    protected function renderHeader(): void
    {
        echo '<h2 class="page-header">';
        echo Html::encode($this->header);
        $subtitle = sprintf(' <small>%s - %s</small>', $this->formatter->asDate($this->diff->getFrom()->getTimestamp()), $this->formatter->asDate($this->diff->getTo()->getTimestamp()));
        echo $subtitle;
//        echo '<sup><span class="fa fa-question-circle-o text-muted"></span></sup>';
        echo '</h2>';
    }

    protected function renderRow(): void
    {
        echo '<div class="row">';
        foreach ($this->statsAttributes as $attribute => $format) {
            $this->renderCell($attribute, $format);
        }
        echo '</div>';
    }
}