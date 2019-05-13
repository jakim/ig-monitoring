<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 06.02.2018
 */

namespace app\modules\admin\widgets;


use app\components\ArrayHelper;
use app\dictionaries\TrackerType;
use app\models\Account;
use app\models\Proxy;
use app\modules\admin\models\MonitoringForm;
use app\modules\admin\widgets\base\ModalWidget;

class CreateMonitoringModal extends ModalWidget
{
    public $trackerType = TrackerType::ACCOUNT;

    public $title;
    public $form;
    public $modalHeader = 'Create monitoring';
    public $modalToggleButton = ['label' => 'Create'];

    protected static $tags;
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
            'tags' => $this->getTagPairs(),
            'proxies' => $this->getProxyPairs(),
        ]);
    }

    protected function getTagPairs()
    {
        if ($this->trackerType == TrackerType::ACCOUNT) {
            return static::$tags = static::$tags ?? ArrayHelper::map(Account::usedTags(), 'name', 'name');
        }

        return false;
    }

    /**
     * @return array
     */
    protected function getProxyPairs(): array
    {
        static::$proxies = static::$proxies ?? Proxy::find()->active()->all();

        return ArrayHelper::map(static::$proxies, 'id', 'ip');
    }

}