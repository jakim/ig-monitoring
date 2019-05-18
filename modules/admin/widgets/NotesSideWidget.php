<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 17.02.2018
 */

namespace app\modules\admin\widgets;


use app\models\AccountNote;
use app\modules\admin\widgets\base\ProfileSideWidget;
use Yii;
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

    protected $note;

    public function init()
    {
        parent::init();
        /** @var AccountNote $model */
        $model = $this->model
            ->getAccountNotes()
            ->limit(1)
            ->one();
        $this->note = $model ? $model->note : null;
    }

    protected function renderModalContent()
    {
        $form = ActiveForm::begin([
            'method' => 'post',
            'action' => ['account/update-note', 'id' => $this->model->id],
        ]);
        echo $form
            ->field(new AccountNote(), 'note')
            ->label(false)
            ->textarea([
                'maxlength' => true,
                'rows' => 5,
                'placeholder' => true,
                'autofocus' => true,
                'value' => $this->note,
            ]);

        echo Html::submitButton('Update', ['class' => 'btn btn-small btn-primary']);

        ActiveForm::end();
    }

    protected function renderBoxContent()
    {
        echo "<p class=\"text-muted\">\n";
        echo Yii::$app->formatter->asNtext($this->note);
        echo "</p>\n";
    }
}