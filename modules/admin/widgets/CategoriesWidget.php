<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 31.01.2018
 */

namespace app\modules\admin\widgets;


use app\components\ArrayHelper;
use app\components\CategoryManager;
use app\models\Account;
use app\models\AccountTag;
use app\modules\admin\models\Tag;
use app\modules\admin\widgets\base\ProfileSideWidget;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\StringHelper;

class CategoriesWidget extends ProfileSideWidget
{
    public $header = 'Categories';
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
     * @var \app\models\Category[]
     */
    protected $modelCategories = [];

    /**
     * @var \app\models\Category[]
     */
    protected $categories = [];

    public function init()
    {
        parent::init();
        $this->modalHeader = $this->header;
        $this->formAction = $this->formAction ?: ['categories', 'id' => $this->model->id];
    }

    public function run()
    {
        $categoryManager = \Yii::createObject(CategoryManager::class);
        /** @var \app\models\User $identity */
        $identity = \Yii::$app->user->identity;

        $this->categories = $categoryManager->getForUser($identity);
        $this->modelCategories = $categoryManager->getForUserAccounts($identity, $this->model);
        parent::run();
    }

    protected function renderBoxContent()
    {
        echo "<p>";
        foreach ($this->modelCategories as $tag) {
            echo sprintf('<span class="label label-default">%s</span> ', Html::encode($tag->name));
        }
        if (!$this->modelCategories) {
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
            'data' => array_combine(ArrayHelper::getColumn($this->categories, 'name'), ArrayHelper::getColumn($this->categories, 'name')),
            'value' => ArrayHelper::getColumn($this->modelCategories, 'name'),
        ]);
        echo "</div>";

        echo Html::submitButton('Update', ['class' => 'btn btn-small btn-primary']);

        echo Html::endForm();
    }
}