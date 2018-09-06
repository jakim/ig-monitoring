<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 06.09.2018
 */

namespace app\modules\admin\widgets;


use app\dictionaries\AccountInvalidationType;
use yii\base\Widget;
use yii\helpers\ArrayHelper;

class InvalidAccountAlert extends Widget
{
    /**
     * @var \app\models\Account
     */
    public $model;

    public function run()
    {
        $icon = $this->model->invalidation_type_id == AccountInvalidationType::IS_PRIVATE ? 'user-secret' : 'exclamation-triangle';
        $alert = $this->model->invalidation_count < 3 ? 'warning' : 'danger';

        return $this->render('invalid-alert', [
            'alert' => $alert,
            'icon' => $icon,
            'header' => 'Invalid account',
            'lines' => $this->lines(),
        ]);
    }

    protected function lines()
    {
        $formatter = \Yii::$app->formatter;

        return [
            sprintf('type: %s', ArrayHelper::getValue(AccountInvalidationType::labels(), $this->model->invalidation_type_id, 'Unknown reason')),
            sprintf('attempts: %s', $formatter->asInteger($this->model->invalidation_count)),
            sprintf('next try: %s', $formatter->asDatetime($this->model->update_stats_after)),
        ];
    }

}