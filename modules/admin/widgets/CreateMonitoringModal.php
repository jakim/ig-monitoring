<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 06.02.2018
 */

namespace app\modules\admin\widgets;


use app\components\ArrayHelper;
use app\components\CategoryManager;
use app\dictionaries\TrackerType;
use app\models\Proxy;
use app\modules\admin\models\MonitoringForm;
use app\modules\admin\widgets\base\ModalWidget;
use Yii;

class CreateMonitoringModal extends ModalWidget
{
    public $trackerType = TrackerType::ACCOUNT;

    public $title;
    public $form;
    public $modalHeader = 'Create monitoring';
    public $modalToggleButton = ['label' => 'Create'];

    protected static $categories;
    protected static $proxies;

    public function run()
    {
        $this->form = $this->form ?: new MonitoringForm(['scenario' => $this->trackerType]);
        parent::run();
    }

    protected function renderModalContent()
    {
        echo $this->render('create-monitoring', [
            'formAction' => ["monitoring/create-{$this->trackerType}"],
            'model' => $this->form,
            'categories' => $this->getCategories(),
        ]);
    }

    protected function getCategories()
    {
        if ($this->trackerType == TrackerType::ACCOUNT) {
            if (!isset(static::$categories)) {
                $categoryManager = Yii::createObject(CategoryManager::class);
                /** @var \app\models\User $identity */
                $identity = Yii::$app->user->identity;
                static::$categories = ArrayHelper::map($categoryManager->getForUser($identity), 'name', 'name');
            }

            return static::$categories;
        }

        return false;
    }
}