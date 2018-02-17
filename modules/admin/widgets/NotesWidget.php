<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 17.02.2018
 */

namespace app\modules\admin\widgets;


use yii\base\Widget;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

class NotesWidget extends Widget
{
    public $header = 'Notes';
    public $headerIcon = 'comment';
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

    public function run()
    {
        echo "<strong>";
        $this->renderHeader();
        echo "</strong>";
        $this->renderPopup();

        $this->renderNotes();
    }

    protected function renderHeader()
    {
        echo "<span class=\"fa fa-{$this->headerIcon} margin-r-5\"></span> {$this->header}";
    }

    protected function renderPopup(): void
    {
        Modal::begin([
            'header' => 'Notes',
            'toggleButton' => [
                'tag' => 'a',
                'label' => 'Update',
                'class' => 'btn btn-xs btn-link',
            ],
        ]);
        $form = ActiveForm::begin(['action' => $this->formAction ?: ['update', 'id' => $this->model->id]]);
        echo $form
            ->field($this->model, 'notes')
            ->label(false)
            ->textarea(['maxlength' => true, 'rows' => 5]);

        echo Html::submitButton('Update', ['class' => 'btn btn-small btn-primary']);

        ActiveForm::end();

        Modal::end();
    }

    private function renderNotes()
    {
        echo "<p class=\"text-muted\">\n";
        echo \Yii::$app->formatter->asNtext($this->model->notes ?: null);
        echo "</p>\n";
    }
}