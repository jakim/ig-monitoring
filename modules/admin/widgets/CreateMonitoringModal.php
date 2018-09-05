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
use yii\helpers\Inflector;

class CreateMonitoringModal extends ModalWidget
{
    public $trackerType = TrackerType::ACCOUNT;

    public $title;
    public $form;
    public $modalHeader = 'Create monitoring';
    public $modalToggleButton = ['label' => 'Create'];

    protected static $tags;
    protected static $proxies;
    protected static $proxyTags;

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
            'proxyTags' => $this->getProxyTagPairs(),
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
        static::$proxies = static::$proxies ?? Proxy::find()->joinWith('tags')->active()->all();

        return ArrayHelper::map(static::$proxies, 'id', function (Proxy $model) {
            $tags = ArrayHelper::getColumn($model->tags, 'name');

            return $model->ip . ($tags ? ' # ' . implode(',', $tags) : '');
        });
    }

    /**
     * @return array
     */
    protected function getProxyTagPairs(): array
    {
        return static::$proxyTags = static::$proxyTags ?? ArrayHelper::map(Proxy::usedTags(), 'id', 'name');
    }

}