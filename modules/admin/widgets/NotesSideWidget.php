<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 17.02.2018
 */

namespace app\modules\admin\widgets;


use app\modules\admin\widgets\base\ProfileSideWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

class NotesSideWidget extends ProfileSideWidget
{
    public $header = 'Notes';
    public $headerIcon = 'comment';
    public $modalToggleButton = [
        'label' => 'Update',
        'class' => 'btn btn-xs btn-link',
    ];

    /**
     * @var array
     */
    public $formAction;
    /**
     * @var \app\modules\admin\models\Account
     */
    public $model;

    public function init()
    {
        parent::init();
        $this->formAction = $this->formAction ?: ['update', 'id' => $this->model->id];
    }

    protected function renderModalContent()
    {
        $form = ActiveForm::begin(['action' => $this->formAction ?: ['update', 'id' => $this->model->id]]);
        echo $form
            ->field($this->model, 'notes')
            ->label(false)
            ->textarea([
                'maxlength' => true,
                'rows' => 5,
                'placeholder' => true,
                'autofocus' => true,
            ]);

        echo Html::submitButton('Update', ['class' => 'btn btn-small btn-primary']);

        ActiveForm::end();
    }

    protected function renderBoxContent()
    {
        echo "<p class=\"text-muted\">\n";
        echo \Yii::$app->formatter->asNtext($this->model->notes ?: null);
        echo "</p>\n";
    }
}