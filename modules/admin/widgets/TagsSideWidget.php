<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 31.01.2018
 */

namespace app\modules\admin\widgets;


use app\components\ArrayHelper;
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
        $this->modelTags = $this->model->tags;
        $this->allTags = $this->getAvailableTags();
        $this->formAction = $this->formAction ?: ['tags', 'id' => $this->model->id];
    }

    private function getAvailableTags()
    {
        $query = Tag::find()
            ->orderBy('name ASC');

        if ($this->model instanceof \app\models\Account) {
            return $query
                ->innerJoinWith('accountTags')
                ->all();

        } elseif ($this->model instanceof \app\models\Tag) {
            return $query
                ->innerJoinWith('accountTags')
                ->all();
        }

        return [];
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