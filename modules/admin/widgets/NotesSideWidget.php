<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 17.02.2018
 */

namespace app\modules\admin\widgets;


use app\models\AccountNote;
use app\modules\admin\widgets\base\ProfileSideWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

class NotesSideWidget extends ProfileSideWidget
{
    public $header = 'Note';
    public $headerIcon = 'comment';
    public $modalToggleButton = [
        'label' => 'Update',
        'class' => 'btn btn-xs btn-link',
    ];

    /**
     * @var \app\modules\admin\models\Account
     */
    public $model;

    protected function renderModalContent()
    {
        $form = ActiveForm::begin([
            'method' => 'post',
            'action' => ['account/update-note', 'id' => $this->model->id],
        ]);
        $note = $this->getNote();
        echo $form
            ->field(new AccountNote(), 'note')
            ->label(false)
            ->textarea([
                'maxlength' => true,
                'rows' => 5,
                'placeholder' => true,
                'autofocus' => true,
                'value' => $note,
            ]);

        echo Html::submitButton('Update', ['class' => 'btn btn-small btn-primary']);

        ActiveForm::end();
    }

    protected function renderBoxContent()
    {
        $note = $this->getNote();
        echo "<p class=\"text-muted\">\n";
        echo \Yii::$app->formatter->asNtext($note);
        echo "</p>\n";
    }

    protected function getNote()
    {
        /** @var AccountNote $note */
        $note = $this->model->getAccountNotes()->limit(1)->one();

        return $note ? $note->note : null;
    }
}