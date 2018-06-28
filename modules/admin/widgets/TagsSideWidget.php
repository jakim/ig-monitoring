<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 31.01.2018
 */

namespace app\modules\admin\widgets;


use app\components\ArrayHelper;
use app\models\AccountTag;
use app\modules\admin\models\Tag;
use app\modules\admin\widgets\base\ProfileSideWidget;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\StringHelper;

class TagsSideWidget extends ProfileSideWidget
{
    public $header = 'Tags';
    public $headerIcon = 'tags';
    public $modalToggleButton = [
        'label' => 'Update',
        'class' => 'btn btn-xs btn-link',
    ];

    /**
     * @var array
     */
    public $formAction;
    /**
     * @var \app\modules\admin\models\Account|\app\modules\admin\models\Tag
     */
    public $model;

    /**
     * @var Tag[]
     */
    protected $modelTags = [];

    /**
     * @var Tag[]
     */
    protected $allTags = [];

    public function init()
    {
        parent::init();
        $userId = (int)\Yii::$app->user->id;
        $this->modelTags = $this->getTags($userId, $this->model->id);
        $this->allTags = $this->getTags($userId);
        $this->formAction = $this->formAction ?: ['tags', 'id' => $this->model->id];
    }

    private function getTags($userId, $accountId = null)
    {
        return Tag::find()
            ->distinct()
            ->innerJoin(AccountTag::tableName(), 'tag.id=account_tag.tag_id AND account_tag.user_id=' . $userId)
            ->andFilterWhere(['account_tag.account_id' => $accountId])
            ->orderBy('name ASC')
            ->all();
    }

    protected function renderBoxContent()
    {
        echo "<p>";
        foreach ($this->modelTags as $tag) {
            echo sprintf('<span class="label label-default">%s</span> ', Html::encode($tag->name));
        }
        if (!$this->modelTags) {
            echo \Yii::$app->formatter->nullDisplay;
        }
        echo "</p>";
    }

    protected function renderModalContent()
    {
        echo Html::beginForm($this->formAction);
        echo "<div class=\"form-group\">";
        echo Select2::widget([
            'name' => strtolower(StringHelper::basename(get_class($this->model)) . '_tags'),
            'options' => [
                'multiple' => true,
                'placeholder' => 'Select tags...',
            ],
            'pluginOptions' => [
                'tags' => true,
            ],
            'data' => array_combine(ArrayHelper::getColumn($this->allTags, 'name'), ArrayHelper::getColumn($this->allTags, 'name')),
            'value' => ArrayHelper::getColumn($this->modelTags, 'name'),
        ]);
        echo "</div>";

        echo Html::submitButton('Update', ['class' => 'btn btn-small btn-primary']);

        echo Html::endForm();
    }
}